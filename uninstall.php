<?php
/**
* @package Recruitment Manager
**/

if (!defined('WP_UNINSTALL_PLUGIN')) {
	die();
}

$option = get_option('cwrm_gen_opt_fields');
if (isset($option['delete_plugin_data'])) {
	cwrm_removePluginSpecificPosts();
	cwrm_deletePluginOptions();
	cwrm_removePluginJobFields();
	cwrm_removeCapsFromAdminRole();
}

function cwrm_removePluginSpecificPosts()
{
	global $wpdb;
	cwrm_removePostAttachments();
	$wpdb->query("
		DELETE FROM {$wpdb->posts} 
		WHERE post_type IN ('cwrm_jobs', 'cwrm_applications');
	");
	$wpdb->query("
		DELETE meta FROM {$wpdb->postmeta} meta 
		LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id 
		WHERE posts.ID IS NULL;
	");
}

function cwrm_deletePluginOptions()
{
	$options = array(
		'cwrm_job_fields',
		'cwrm_job_opt_fields',
		'cwrm_res_opt_fields',
		'cwrm_gen_opt_fields',
		'cwrm_auth_opt_fields',
		'cwrm_mail_opt_fields',
		'cwrm_mail_new_user_field',
		'cwrm_mail_new_application_admin_field',
		'cwrm_mail_new_application_user_field',
		'cwrm_mail_email_verif_field',
		'cwrm_mail_forgot_password_field',
		'cwrm_css_field',
	);

	foreach ($options as $option) {
		delete_option($option);
	}
}

function cwrm_removePluginJobFields()
{
	global $wpdb;
	$filters = get_option('cwrm_job_fields');
	if (!empty($filters)) {
		foreach ($filters as $filter) {
			$taxonomy = $filter['singular_name'];
			$terms = cwrm_getJobFields($taxonomy);
			if (!empty($terms)) {
				foreach ($terms as $term) {
					$wpdb->delete($wpdb->term_taxonomy, array('term_taxonomy_id' => $term->term_taxonomy_id), array('%d'));
					$wpdb->delete($wpdb->term_relationships, array('term_taxonomy_id' => $term->term_taxonomy_id), array('%d'));
					$wpdb->delete($wpdb->terms, array('term_id' => $term->term_id), array('%d'));
				}
			}
			$wpdb->delete($wpdb->term_taxonomy, array('taxonomy' => $taxonomy));
		}
	}
}

function cwrm_getJobFields($taxonomy)
{
	return $wpdb->get_results($wpdb->prepare("
		SELECT t.term_id, tt.term_taxonomy_id 
		FROM {$wpdb->terms} t 
		INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
		WHERE tt.taxonomy IN ( %s )
	", $taxonomy));
}

function cwrm_removePostAttachments()
{
	global $wpdb;
	$attachments = $wpdb->get_results("
		SELECT posts.ID 
		FROM {$wpdb->posts} posts 
		INNER JOIN {$wpdb->posts} parent ON posts.post_parent = parent.ID 
		WHERE posts.post_type = 'attachment' 
		AND parent.post_type = 'cwrm_app_pt'
	");
	if (!empty($attachments)) {
		foreach ($attachments as $attachment) {
			wp_delete_attachment($attachment->ID, true);
		}
	}
}

function cwrm_removeCapsFromAdminRole() {
	$caps = cwrm_getCaps2(array('jobs', 'applications', 'general'));
	$admin = get_role('administrator');
	if ($admin) {
		foreach ($caps as $cap) {
			if ($admin->has_cap($cap)) {
				$admin->remove_cap($cap);
			}
		}
	}
	$admin2 = get_role('wp_smart_recruit_admin');
	if ($admin2) {
		foreach ($caps as $cap) {
			if ($admin2->has_cap($cap)) {
				$admin2->remove_cap($cap);
			}
		}
		remove_role('wp_smart_recruit_admin');
	}
}

function cwrm_getCaps2($types = array()) {
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
		$caps[] = 'settings_resume_cwrm';
		$caps[] = 'settings_auth_cwrm';
		$caps[] = 'settings_email_cwrm';
		$caps[] = 'settings_css_cwrm';
	}

	return $caps;
}