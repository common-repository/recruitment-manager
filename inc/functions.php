<?php

/**
* @package Recruitment Manager
**/


if (!function_exists('cwrm_vardump')) {
	function cwrm_vardump($data, $stop = true)
	{
		echo "<pre>";
		print_r($data);
		if ($stop) {
			exit;
		}
	}
}

if (!function_exists('cwrm_getData')) {
	function cwrm_getData($name, $type = 'text')
	{
		if (isset($_POST[$name]) && $_POST[$name] != '') {
			if ($type == 'text') {
				return sanitize_text_field($_POST[$name]);
			} elseif ($type == 'email') {
				return sanitize_email($_POST[$name]);
			} elseif ($type == 'textarea') {
				return cwrm_sanitizeTextarea($_POST[$name]);
			} elseif ($type == 'ids') {
				return array_map('sanitize_text_field', $_POST[$name]);
			} elseif ($type == 'meta-fields') {
				return cwrm_sanitizeMetaFields($_POST[$name]);
			}
		} elseif (isset($_GET[$name]) && $_GET[$name] != '') {
			if ($type == 'text') {
				return sanitize_text_field($_GET[$name]);
			}
		}
	}
}

if (!function_exists('cwrm_validateInput')) {
	function cwrm_validateInput($fields, $rules, $names = array())
	{
		$array = array();
		$list = '';
		foreach ($fields as $key => $value) {
			if (isset($rules[$key])) {
				$validations = explode('|', $rules[$key]);
				foreach ($validations as $validation) {
					$minlen = cwrm_explodeAndGetvalue($validation, 'minlen');
					$maxlen = cwrm_explodeAndGetvalue($validation, 'maxlen');
					$retype = cwrm_explodeAndGetvalue($validation, 'retype');
					$gt_dat = cwrm_explodeAndGetvalue($validation, 'gt_dat');
					$gt_num = cwrm_explodeAndGetvalue($validation, 'gt_num');

					$fieldName = (isset($names[$key]) ? $names[$key] : strtoupper($key));

					//For required
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($validation == 'required' && empty($v)) {
								$message = $fieldName.__(' is required', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($validation == 'required' && empty($value)) {
							$message = $fieldName.__(' is required', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For minimum length
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($v && $minlen && strlen($v) < $minlen) {
								$message = $fieldName.__(' should be at least ', 'wp-recruit-manager').$minlen.__(' characters', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($value && $minlen && strlen($value) < $minlen) {
							$message = $fieldName.__(' should be at least ', 'wp-recruit-manager').$minlen.__(' characters', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For max length
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($v && $maxlen && strlen($v) > $maxlen) {
								$message = $fieldName.__(' can not be more than ', 'wp-recruit-manager').$maxlen.__(' characters', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($value && $maxlen && strlen($value) > $maxlen) {
							$message = $fieldName.__(' can not be more than ', 'wp-recruit-manager').$maxlen.__(' characters', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For number
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($validation == 'number' && $v && !number_format($v)) {
								$message = $fieldName.__(' should be a valid number', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($validation == 'number' && $value && !number_format($value)) {
							$message = $fieldName.__(' should be a valid number', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For string with space and numbers allowed
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($validation == 'alpha_num_space' && $v && !preg_match('/^[a-z0-9 \-]+$/i', $v)) {
								$message = $fieldName.__(' can only contain alphabets, numbers and spaces', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($validation == 'alpha_num_space' && $value && !preg_match('/^[a-z0-9 \-]+$/i', $value)) {
							$message = $fieldName.__(' can only contain alphabets, numbers and spaces', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For email
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($validation == 'email' && $v && !filter_var($v, FILTER_VALIDATE_EMAIL)) {
								$message = $fieldName.__(' should be a valid email address', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($validation == 'email' && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
							$message = $fieldName.__(' should be a valid email address', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For date
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($validation == 'date' && $v && DateTime::createFromFormat('Y-m-d H:i:s', $v) !== FALSE) {
								$message = $fieldName .__(' should be a valid date', 'wp-recruit-manager');
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($validation == 'date' && $value && DateTime::createFromFormat('Y-m-d H:i:s', $value) !== FALSE) {
							$message = $fieldName .__(' should be a valid date', 'wp-recruit-manager');
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For retype
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($v && $retype && (isset($fields[$retype][$k]) && $v != $fields[$retype][$k])) {
								$message = $fieldName.__(' should match ', 'wp-recruit-manager').(isset($names[$retype]) ? $names[$retype] : strtoupper($retype));
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($value && $retype && (isset($fields[$retype]) && $value != $fields[$retype])) {
							$message = $fieldName.__(' should match ', 'wp-recruit-manager').(isset($names[$retype]) ? $names[$retype] : strtoupper($retype));
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For greater than date
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($v && $gt_dat && (isset($fields[$gt_dat][$k]) 
								&& strtotime($v) <= strtotime($fields[$gt_dat][$k]))) {
								$message = $fieldName.__(' should be greater than ', 'wp-recruit-manager').(isset($names[$gt_dat]) ? $names[$gt_dat] : strtoupper($gt_dat));
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($value && $gt_dat && (isset($fields[$gt_dat]) 
							&& strtotime($value) < strtotime($fields[$gt_dat]))) {
							$message = $fieldName.__(' should match ', 'wp-recruit-manager').(isset($names[$gt_dat]) ? $names[$gt_dat] : strtoupper($gt_dat));
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

					//For greater than number
					if (is_array($value)) {
						foreach ($value as $k => $v) {
							if ($v && $gt_num && (isset($fields[$gt_num][$k]) 
								&& $v <= $fields[$gt_num][$k])) {
								$message = $fieldName.__(' should be greater than ', 'wp-recruit-manager').(isset($names[$gt_num]) ? $names[$gt_num] : strtoupper($gt_num));
								$array[] = $message;
								$list .= '<li>'.$message.'</li>';
								break;
							}
						}
					} else {
						if ($value && $gt_num && (isset($fields[$gt_num]) 
							&& $value < strtotime($fields[$gt_num]))) {
							$message = $fieldName.__(' should match ', 'wp-recruit-manager').(isset($names[$gt_num]) ? $names[$gt_num] : strtoupper($gt_num));
							$array[] = $message;
							$list .= '<li>'.$message.'</li>';
						}
					}

				}
			}		
		}
		$list = $list ? '<ul>'.$list.'</ul>' : '';
		return array('array' => $array, 'list' => $list);
	}
}

if (!function_exists('cwrm_explodeAndGetvalue')) {
	function cwrm_explodeAndGetvalue($string, $value)
	{
		if (strpos($string, $value) !== false) {
		    $exploded = explode(':', $string);
		    return isset($exploded[1]) ? $exploded[1] : '';
		}
	}
}

if (!function_exists('cwrm_trimString')) {
	function cwrm_trimString($string, $length = 100)
	{
		return $length != 0 && $length ? substr(strip_tags($string),0,$length).'...' : '';
	}
}

if (!function_exists('cwrm_taxFiltersData')) {
	function cwrm_taxFiltersData()
	{
		if (isset($_POST['filters'])) {
			$prepared = cwrm_sanitizeFilters($_POST['filters']);
			if (cwrm_getData('sc_field')) {
				$prepared[] = array(
					'taxonomy' => cwrm_getData('sc_field'),
					'field' => 'slug',
					'terms' => cwrm_getData('sc_value')
				);
			}
			if ($prepared) {
				return array_merge(array('relation' => 'AND'), $prepared);
			}
		}
	}
}

if (!function_exists('cwrm_getActualTaxonomyName')) {
	function cwrm_getActualTaxonomyName($name)
	{
		$jobFields = get_option('cwrm_job_fields');
		foreach ($jobFields as $value) {
			if (strtolower($name) == strtolower($value['singular_name'])) {
				return $value['singular_name'];
			}
		}
	}
}

if (!function_exists('cwrm_argsDataForJobsWpQuery')) {
	function cwrm_argsDataForJobsWpQuery()
	{
		$job_limit = cwrm_getOption('cwrm_job_opt_fields', 'job_limit');
		$job_limit = $job_limit ? $job_limit : 5;
		$taxFiltersData = cwrm_taxFiltersData();
		$search = cwrm_getData('search');
		$min_salary = cwrm_getData('min_salary');
		$max_salary = cwrm_getData('max_salary');
		
		//cwrm_gen_opt_fields
		$page = cwrm_getData('page');
		$offset = $page == 1 ? 0 : (($page - 1)*$job_limit);

		//Preparing general arguments
	    $args['post_type'] = CW_WP_RM_JOB_POST_TYPE;
	    $args['post_status'] = 'publish';
	    $args['posts_per_page'] = $job_limit;
	    $args['offset'] = $offset;

	    //If keywords search is made
	    if ($search) {
	    	$args['s'] = $search;
	    }

	    //If filters are selected
	    if ($taxFiltersData) {
	    	$args['tax_query'] = $taxFiltersData;
	    }

	    //If salary search is made
	    if ($min_salary && $max_salary) {
	    	$args['meta_query']['ralation'] = 'AND';
	    	$args['meta_query'][] = array(
	            'key' => '_cwrm_job_min_salary',
	            'value' => $min_salary,
	            'type' => 'numeric',
	            'compare' => '>='
	        );
	    	$args['meta_query'][] = array(
	            'key' => '_cwrm_job_max_salary',
	            'value' => $max_salary,
	            'type' => 'numeric',
	            'compare' => '<='
	        );
	    } elseif ($min_salary && !$max_salary) {
	    	$args['meta_query'][] = array(
	            'key' => '_cwrm_job_min_salary',
	            'value' => $min_salary,
	            'type' => 'numeric',
	            'compare' => '>='
	        );
	    } elseif ($max_salary && !$min_salary) {
	    	$args['meta_query'][] = array(
	            'key' => '_cwrm_job_max_salary',
	            'value' => $max_salary,
	            'type' => 'numeric',
	            'compare' => '<='
	        );
	    }

	   	//Implementing date restriction if enabled
	   	if (cwrm_getOption('cwrm_job_opt_fields', 'hide_job_after_last_date')) {
		    $args['meta_query'][] = array(
		        array(
		            'key' => '_cwrm_job_last_date',
		            'value' => date('Y-m-d G:i:s'),
		            'compare' => '>=',
		            'type' => 'DATE'
		        )
		    );
	   	}

	    return $args;
	}
}

if (!function_exists('cwrm_getJobs')) {
	function cwrm_getJobs()
	{
		$result = array();
		$args = cwrm_argsDataForJobsWpQuery();
		$query = new WP_Query($args);
		if ($query->have_posts()) :
			$desc_length = cwrm_getOption('cwrm_job_opt_fields', 'list_description_length');
			$desc_length = $desc_length == 0 || $desc_length ? $desc_length : 100;
			while ($query->have_posts()) : $query->the_post();
				$result[] = array(
					'id' => get_the_ID(),
					'min_salary' => get_post_meta( get_the_ID(), '_cwrm_job_min_salary', true ) ?: '',
					'max_salary' => get_post_meta( get_the_ID(), '_cwrm_job_max_salary', true ) ?: '',
					'last_date' => get_post_meta( get_the_ID(), '_cwrm_job_last_date', true ) ?: '',
					'title' => get_the_title(),
					'excerpt' => cwrm_trimString(get_the_excerpt(), $desc_length),
					'link' => get_permalink(),
					'terms' => cwrm_getPostTerms(get_the_ID())
				);
			endwhile;
		endif;
		wp_reset_postdata();
		return $result;
	}
}

if (!function_exists('cwrm_getJobsForTaxonomy')) {
	function cwrm_getJobsForTaxonomy($taxonomy, $term)
	{
		$tax_data[] = array(
			'taxonomy' => ucfirst($taxonomy),
			'field' => 'slug',
			'terms' => $term
		);
		$tax_data = array_merge(array('relation' => 'AND'), $tax_data);
	    $args['post_type'] = CW_WP_RM_JOB_POST_TYPE;
	    $args['post_status'] = 'publish';
	    $args['posts_per_page'] = 100000;
	    $args['offset'] = 0;
	    $args['tax_query'] = $tax_data;
		$result = array();
		$query = new WP_Query($args);
		if ($query->have_posts()) :
			$desc_length = cwrm_getOption('cwrm_job_opt_fields', 'list_description_length');
			$desc_length = $desc_length == 0 || $desc_length ? $desc_length : 100;
			while ($query->have_posts()) : $query->the_post();
				$result[] = array(
					'id' => get_the_ID(),
					'min_salary' => get_post_meta( get_the_ID(), '_cwrm_job_min_salary', true ) ?: '',
					'max_salary' => get_post_meta( get_the_ID(), '_cwrm_job_max_salary', true ) ?: '',
					'last_date' => get_post_meta( get_the_ID(), '_cwrm_job_last_date', true ) ?: '',
					'title' => get_the_title(),
					'excerpt' => cwrm_trimString(get_the_excerpt(), $desc_length),					
					'link' => get_permalink(),
					'terms' => cwrm_getPostTerms(get_the_ID())
				);
			endwhile;
		endif;
		wp_reset_postdata();
		return $result;
	}
}

if (!function_exists('cwrm_getJobsApplications')) {
	function cwrm_getJobsApplications()
	{
		$user = wp_get_current_user();
	    $args['post_type'] = CW_WP_RM_APP_POST_TYPE;
	    $args['post_status'] = 'publish';
	    $args['author'] = $user->data->ID;
	    $args['posts_per_page'] = 100000;
	    $args['offset'] = 0;
		$result = array();
		$query = new WP_Query($args);
		if ($query->have_posts()) :
			while ($query->have_posts()) : $query->the_post();
				$result[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'date' => get_the_date(),
					'link' => get_permalink(),
				);
			endwhile;
		endif;
		wp_reset_postdata();
		return $result;
	}
}

if (!function_exists('cwrm_getJobsTitles')) {
	function cwrm_getJobsTitles($taxonomy, $term)
	{
	    $args['post_type'] = CW_WP_RM_JOB_POST_TYPE;
	    $args['post_status'] = 'publish';
	    $args['posts_per_page'] = 100000;
	    $args['offset'] = 0;

	    if ($taxonomy) {
			$tax_data[] = array(
				'taxonomy' => ucfirst($taxonomy),
				'field' => 'slug',
				'terms' => $term
			);
			$tax_data = array_merge(array('relation' => 'AND'), $tax_data);
	    	$args['tax_query'] = $tax_data;
	    }

		$result = array();
		$query = new WP_Query($args);
		if ($query->have_posts()) :
			while ($query->have_posts()) : $query->the_post();
				$result[] = array(
					'id' => get_the_ID(),
					'title' => get_the_title(),
					'link' => get_permalink(),
				);
			endwhile;
		endif;
		wp_reset_postdata();
		return $result;
	}
}

if (!function_exists('cwrm_getPostTerms')) {
	function cwrm_getPostTerms($id)
	{
		$result = array();
		$taxonomies = get_object_taxonomies(CW_WP_RM_JOB_POST_TYPE);
		foreach ($taxonomies as $taxonomy) {
			$terms = wp_get_post_terms($id, $taxonomy);
			if ($terms) {
				foreach ($terms as $term) {
					$result[$taxonomy][] = $term->name;
				}
			}
		}
		return $result;
	}
}

if (!function_exists('cwrm_getTaxonomies')) {
	function cwrm_getTaxonomies($type = CW_WP_RM_JOB_POST_TYPE)
	{
		$result = array();
		$taxonomies = get_object_taxonomies($type);
		foreach ($taxonomies as $taxonomy) {
			$terms = get_terms([
			    'taxonomy' => $taxonomy,
			    'hide_empty' => false,
			]);
			if ($terms) {
				foreach ($terms as $term) {
					$result[$taxonomy][] = array(
						'name' => $term->name,
						'slug' => $term->slug,
					);
				}
			}
		}
		return $result;
	}
}

if (!function_exists('cwrm_sanitizeTextarea')) {
	function cwrm_sanitizeTextarea( $input ) {
		if ( function_exists('sanitize_textarea_field' ) ) {
			$input = sanitize_textarea_field( $input );
		} else {
			$input = esc_textarea( $input );
		}
		return $input;
	}
}

if (!function_exists('cwrm_sanitizeMetaFields')) {
	function cwrm_sanitizeMetaFields( $input ) {
		$return = array();
		foreach ($input as $key => $value) {
			$return[sanitize_text_field($key)] = sanitize_text_field($value);
		}
		return $return;
	}
}

if (!function_exists('cwrm_sanitizeFilters')) {
	function cwrm_sanitizeFilters( $input ) {
		$prepared = array();
		$filters = json_decode(stripslashes($input), TRUE);
		foreach ($filters as $filter => $slugs) {
			foreach ($slugs as $slug) {
				if ($slug) {
					$prepared[] = array(
						'taxonomy' => sanitize_text_field($filter),
						'field' => 'slug',
						'terms' => sanitize_text_field($slug)
					);
				}
			}
		}
		return $prepared;
	}
}

if (!function_exists('cwrm_getUserIP')) {
	function cwrm_getUserIP() {
		return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
	}
}

if (!function_exists('cwrm_sortForCSV')) {
	function cwrm_sortForCSV($data)
	{
		$excelData = array_values($data);
	    $return = array();
	    $keys = array_keys(max($data));
	    for ($i=0; $i < count($excelData) ; $i++) { 
	        foreach ($keys as $key) {
	            $return[$i][] = isset($excelData[$i][$key]) ? $excelData[$i][$key] : '';
	        }
	    }
	    $return = array_merge(array($keys), $return);
	    return $return;
	}
}

if (!function_exists('cwrm_objToArr')) {
	function cwrm_objToArr($obj) {
	    return json_decode(json_encode($obj), true);
	}
}

if (!function_exists('cwrm_arrangeSections')) {
	function cwrm_arrangeSections($data)
	{
	    $return = array();
	    $keys = array();
	    foreach ($data as $key => $value) {
	        $keys[] = $key;
	    }
	    for ($i=0; $i < count(array_values($data)[0]) ; $i++) { 
	        foreach ($keys as $key) {
	            $return[$i][$key] = $data[$key][$i]; 
	        }
	    }
	    return $return;
	}
}

if (!function_exists('cwrm_sel')) {
	function cwrm_sel($val, $sel, $text = '') {
	    echo $val == $sel ? ($text ? esc_attr($text) : 'selected') : '';
	}
}

if (!function_exists('cwrm_spaceToUnderscore')) {
	function cwrm_spaceToUnderscore($string) {
	    return preg_replace('/\s+/', '_', $string);
	}
}

if (!function_exists('cwrm_getStrToTime')) {
	function cwrm_getStrToTime() {
	    return strtotime(date('Y-m-d G:i:s'));
	}
}

if (!function_exists('cwrm_var')) {
	function cwrm_var($data, $value, $label = '') {
		if ($label && $value) {
	    	return isset($data[$value]) ? $label.' '.$data[$value] : '';
		} else {
	    	return isset($data[$value]) ? $data[$value] : '';
		}
	}
}

if (!function_exists('cwrm_currentUrl')) {
	function cwrm_currentUrl() {
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
			$protocol = 'https';
		} else {
			$protocol = 'http';
		}
		return $protocol."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
}

if (!function_exists('cwrm_activeTab')) {
	function cwrm_activeTab($field, $value, $type = 'full') {
		if ($field == $value) {
			if ($type == 'full') {
				echo 'nav-tab-active';
			} else {
				return true;
			}
		}
	}
}

if (!function_exists('cwrm_echoFile')) {
	function cwrm_echoFile($file) {
		ob_start();
		require_once($file);
		echo ob_get_clean();
	}
}

if (!function_exists('cwrm_validNumber')) {
	function cwrm_validNumber($string) {
		if (is_integer($string)) {
			return true;
		} elseif (is_float($string)) {
			return true;
		}
		return false;
	}
}

if (!function_exists('cwrm_getOption')) {
	function cwrm_getOption($name, $key = '') {
		$option = get_option($name);
		if ($key == '') {
			return $option;
		} else {
			return isset($option[$key]) ? $option[$key] : '';
		}
	}
}

if (!function_exists('cwrm_ifJobFieldEnabled')) {
	function cwrm_ifJobFieldEnabled($name) {
		$fields = get_option('cwrm_job_fields');
		foreach ($fields as $key => $value) {
			if ($name == $value['singular_name']) {
				return isset($value['front_end_values']) ? $value['front_end_values'] : '';
			}
		}
	}
}

if (!function_exists('cwrm_replaceHyphen')) {
	function cwrm_replaceHyphen($name) {
		return str_replace('-', ' ', $name);
	}
}

if (!function_exists('cwrm_ifJobFilterEnabled')) {
	function cwrm_ifJobFilterEnabled($fields, $name) {
		foreach ($fields as $key => $value) {
			if ($name == $value['singular_name']) {
				return isset($value['front_end_filters']) ? $value['front_end_filters'] : '';
			}
		}
	}
}

if (!function_exists('cwrm_userData')) {
	function cwrm_userData($column = '', $meta = '') {
		$user = wp_get_current_user();
		if ($user) {
			if ($column == '') {
				return $user->data;
			} elseif($column == 'id') {
				return $user->data->ID;
			} elseif (isset($user->data->{$column})) {
				return isset($user->data->{$column}) ? $user->data->{$column} : '';
			} else {
				$meta = get_user_meta($user->data->ID, $column);
				return isset($meta[0]) ? $meta[0] : '';
			}
		}
	}
}

if (!function_exists('cwrm_getUserData')) {
	function cwrm_getUserData($id) {
		$user = get_user_meta($id);
		return $user;
	}
}

if (!function_exists('cwrm_meta')) {
	function cwrm_meta($data, $name, $dash = '') {
		return isset($data[$name][0]) ? $data[$name][0] : ($dash ? '---' : '');
	}
}

if (!function_exists('cwrm_slugify')) {
	function cwrm_slugify($text = '') {
		if ($text == '') {
			return strtotime(date('Y-m-d G:i:s'));
		}

		$text = preg_replace('~[^\pL\d]+~u', '-', $text);
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
		$text = preg_replace('~[^-\w]+~', '', $text);
		$text = trim($text, '-');
		$text = preg_replace('~-+~', '-', $text);
		$text = strtolower($text);
		if (empty($text)) {
			return 'n-a';
		}
		return $text;
	}
}

if (!function_exists('cwrm_getWidgetDataForAllSidebars')) {
	function cwrm_getWidgetDataForAllSidebars() {
		global $wp_registered_sidebars;
		$output = array();
		foreach ($wp_registered_sidebars as $sidebar) {
			if (empty( $sidebar['name'])) {
				continue;
			}
			$sidebar_name = $sidebar['name'];
			$output[ $sidebar_name ] = cwrm_getWidgetDataForSidebar($sidebar_name);
		}
		return $output;
	}
}

if (!function_exists('cwrm_getWidgetDataForSidebar')) {
	function cwrm_getWidgetDataForSidebar($sidebar_name) {
		global $wp_registered_sidebars, $wp_registered_widgets;

		$output = array();
		$sidebar_id = false;
		foreach ($wp_registered_sidebars as $sidebar) {
			if ($sidebar['name'] == $sidebar_name) {
				$sidebar_id = $sidebar['id'];
				break;
			}
		}

		if (!$sidebar_id) {
			return $output;
		}

		$sidebars_widgets = wp_get_sidebars_widgets();
		$widget_ids = $sidebars_widgets[ $sidebar_id ];

		if (!$widget_ids) {
			return array();
		}

		foreach ( $widget_ids as $id ) {
			$option_name = $wp_registered_widgets[$id]['callback'][0]->option_name;
			$key = $wp_registered_widgets[$id]['params'][0]['number'];
			$widget_data = get_option($option_name);
			$output[] = (object) $widget_data[$key];
		}

		return $output;
	}
}

if (!function_exists('cwrm_addWpsrRole')) {
	function cwrm_addWpsrRoles() {
		//Adding admin role
		$role = 'cwrm_admin';
		$name = 'Recruitment Manager Admin';
		$caps = cwrm_getCaps(array('jobs', 'applications', 'general'));
		if (!get_role($role)) {
			add_role($role, $name, $caps);
			$admin = get_role($role);
			foreach ($caps as $cap) {
				if (!$admin->has_cap($cap)) {
					$admin->add_cap($cap);
				}
			}
		}

		//Adding applicant
		if (!get_role('cwrm_applicant')) {
			add_role('cwrm_applicant', 'Recruitment Manager Applicant', array());
		}
	}
}

if (!function_exists('cwrm_addCapsToAdminRole')) {
	function cwrm_addCapsToAdminRole() {
		$caps = cwrm_getCaps(array('jobs', 'applications', 'general'));
		$admin = get_role('administrator');
		foreach ($caps as $cap) {
			if (!$admin->has_cap($cap)) {
				$admin->add_cap($cap);
			}
		}
	}
}

if (!function_exists('cwrm_getCaps')) {
	function cwrm_getCaps($types = array()) {
		$caps[] = 'read';
		$caps[] = 'edit_posts';

		if (in_array('jobs', $types)) {
			$caps[] = 'create_jobs_cwrm';
			$caps[] = 'delete_jobs_cwrm';
			$caps[] = 'delete_others_jobs_cwrm';
			$caps[] = 'delete_private_jobs_cwrm';
			$caps[] = 'delete_published_jobs_cwrm';
			$caps[] = 'edit_jobs_cwrm';
			$caps[] = 'edit_others_jobs_cwrm';
			$caps[] = 'edit_private_jobs_cwrm';
			$caps[] = 'edit_published_jobs_cwrm';
			$caps[] = 'publish_jobs_cwrm';
			$caps[] = 'read_private_jobs_cwrm';
		}

		if (in_array('applications', $types)) {
			$caps[] = 'delete_applications_cwrm';
			$caps[] = 'delete_others_applications_cwrm';
			$caps[] = 'delete_private_applications_cwrm';
			$caps[] = 'delete_published_applications_cwrm';
			$caps[] = 'edit_applications_cwrm';
			$caps[] = 'edit_others_applications_cwrm';
			$caps[] = 'edit_private_applications_cwrm';
			$caps[] = 'edit_published_applications_cwrm';
			$caps[] = 'publish_applications_cwrm';
			$caps[] = 'read_private_applications_cwrm';
		}

		if (in_array('general', $types)) {
			$caps[] = 'job_fields_cwrm';
			$caps[] = 'job_fields_add_cwrm';
			$caps[] = 'job_fields_edit_cwrm';
			$caps[] = 'job_fields_delete_cwrm';
			$caps[] = 'settings_cwrm';
			$caps[] = 'settings_shortcodes_cwrm';
			$caps[] = 'settings_general_cwrm';
			$caps[] = 'settings_job_cwrm';
			$caps[] = 'settings_email_cwrm';
			$caps[] = 'settings_css_cwrm';
		}

		return $caps;
	}
}

if (!function_exists('cwrm_getPostTypeShowInMenuAttr')) {
	function cwrm_getPostTypeShowInMenuAttr($pt = '') {
		return 'edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE;
		if ($pt == 'application' || $pt == 'resume') {
		}
	 	$show_in_menu = current_user_can('edit_jobs_cwrm') && current_user_can('edit_posts');
	 	return $show_in_menu ? 'edit.php?post_type='.CW_WP_RM_JOB_POST_TYPE : true;
	}
}

if (!function_exists('cwrm_encodeDecode')) {
	function cwrm_encodeDecode( $string, $action = 'e' ) {
	    $secret_key = '!2=&)P8z!ouw';
	    $secret_iv = 'mysimplesecretiv';

	    $output = false;
	    $encrypt_method = "AES-256-CBC";
	    $key = hash( 'sha256', $secret_key );
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

	    if( $action == 'e' ) {
	        $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
	    }
	    else if( $action == 'd' ){
	        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
	    }

	    return $output;
	}
}

if (!function_exists('cwrm_issetVal'))
{
function cwrm_issetVal($array, $index)
{
    return isset($array[$index]) ? $array[$index] : '';
}
}

if (!function_exists('cwrm_checkExistingApplication')) {
	function cwrm_checkExistingApplication($job_id, $secondParam)
	{
		if (is_numeric($secondParam)) {
			$args = array(
			    'post_type'  => CW_WP_RM_APP_POST_TYPE,
			    'author' => $secondParam,
			    'post_parent' => $job_id,
			);
			$my_query = new WP_Query($args);
		} else {
			$my_query = new WP_Query(array(
			    'post_type'  => CW_WP_RM_APP_POST_TYPE,
			    'meta_query' => array(
			    	'relation' => 'AND',
			        array(
			            'key' => '_cwrm_job_id',
			            'compare' => '=',
			            'value' => $job_id
			        ),
			        array(
			            'key'     => '_cwrm_applicant_email',
			            'compare' => '=',
			            'value' => $secondParam
			        ),
			    )
			));
		}
		if ( $my_query->have_posts() ): while ( $my_query->have_posts() ): $my_query->the_post();
		    return true;
		endwhile; endif;
		wp_reset_postdata();		
	}
}
