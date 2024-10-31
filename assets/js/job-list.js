jQuery(document).ready(function() {

    "use strict";

    var job_list_container_id = '#cwrm-list-jobs-container';
    var single_job_list_container_id = '#cwrm-single-jobs-list-container';
    var existing_job_detail_container_id = '#cwrm-job-detail-container';
    var job_page_id = '#cwrm-job-page';
    var load_jobs_btn = '#cwrm-load-jobs-btn';
    var search_btn = '#cwrm-filter-btn';

    jQuery(document).off('click', search_btn);
    jQuery(document).on('click', search_btn, function() {
        cwrm_resetPageAndFetchResults();
    });

    jQuery(document).off('click', load_jobs_btn);
    jQuery(document).on('click', load_jobs_btn, function() {
        jQuery(load_jobs_btn).html(jQuery("#cwrm-loading-text").val());
        jQuery(load_jobs_btn).attr('disabled', true);
        cwrm_fetchJobs(true);
    });

    jQuery(document).off('click', '#cwrm-reset-filters');
    jQuery(document).on('click', '#cwrm-reset-filters', function() {
        jQuery('.cwrm-side-search-box').val('');
        jQuery('.cwrm-top-search-box').val('');
        jQuery('.cwrm-job-filter-dd').each(function(i, v) {
            jQuery(this).val('');
        })
        jQuery(job_page_id).val(1);
        cwrm_fetchJobs();
    });

    function cwrm_resetPageAndFetchResults() {
        jQuery(job_page_id).val(1);
        jQuery(job_list_container_id).html('<span class="search-span">'+jQuery('#cwrm-search-title').val()+'<span>');
        jQuery(single_job_list_container_id).html('<span class="search-span">'+jQuery('#cwrm-search-title').val()+'<span>');
        jQuery(existing_job_detail_container_id).remove();
        jQuery('.entry-title').hide();
        jQuery('.entry-meta').hide();
        cwrm_fetchJobs();
    }

    function cwrm_fetchJobs(append = false) {
        let params = new URLSearchParams(cwrm_filtersData());
        fetch(jQuery('#ajax-url').val(), {
            method: "POST",
            body: params
        }).then(res => {
            return res.text();
        }).then(function (data) {
            if (data) {
                if (append) {
                    jQuery(job_list_container_id).append(data);
                } else {
                    if (jQuery('#cwrm-list-jobs-container').length) {
                        jQuery(job_list_container_id).html(data);
                    } else {
                        jQuery(single_job_list_container_id).html(data);
                    }
                }
                jQuery(load_jobs_btn).html(jQuery("#cwrm-load-more").val());
                jQuery(load_jobs_btn).attr('disabled', false);
                var page = jQuery(job_page_id).val();
                jQuery(job_page_id).val(parseInt(page) + 1);
            } else {
                jQuery(load_jobs_btn).html(jQuery('#no-more-jobs').val());
                jQuery('.search-span').remove();
                jQuery(load_jobs_btn).attr('disabled', true);
            }
        }).catch(error => {
        }).then(response => {
        });
    }

    function cwrm_filtersData() {
        var filters = {};
        jQuery(".cwrm-job-filter-dd").each(function(i, v) {
            filters[jQuery(this).data("filter")] = [];
        });
        jQuery(".cwrm-job-filter-dd").each(function(i, v) {
            filters[jQuery(this).data("filter")].push(jQuery(this).val());
        });
        return {
            filters: JSON.stringify(filters),
            search: jQuery("#cwrm-search-key-box").length ? jQuery("#cwrm-search-key-box").val() : '',
            min_salary: jQuery('#cwrm-min-salary-box').length ? jQuery('#cwrm-min-salary-box').val() : '',
            max_salary: jQuery('#cwrm-max-salary-box').length ? jQuery('#cwrm-max-salary-box').val() : '',
            page: jQuery("#cwrm-job-page").length ? jQuery("#cwrm-job-page").val() : '',
            nonce: jQuery("#nonce").length ? jQuery("#nonce").val() : '',
            list_page: jQuery('#cwrm-list-jobs-container').length ? 'true' : 'false',
            action: "cwrm_fetch_jobs",
        }
    }

    if (jQuery('#cwrm-list-jobs-container').length) {
        cwrm_fetchJobs();
    }

});
