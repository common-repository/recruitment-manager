<!-- Jobs Application List container Start -->
<div class="cwrm-jobs-container cwrm-applied-jobs-container" id="cwrm-applied-jobs-container">

<?php $applications = cwrm_getJobsApplications(); ?>

<?php if ($applications) { ?>
<?php foreach ($applications as $app) { ?>
    <div class="cwrm-jobs-item cwrm-jobs-item-<?php esc_attr_e($app['id']); ?>">
        <h2 class="entry-title cwrm-list-page-title cwrm-list-page-title-<?php esc_attr_e($app['id']); ?>">
            <a href="<?php echo esc_url($app['link']); ?>" ><?php esc_html_e($app['title']); ?></a>
        </h2>
        <div class="cwrm-list-page-desc cwrm-list-page-desc-<?php esc_attr_e($app['id']); ?>">
        <p><?php _e('Applied On', 'wp-recruit-manager'); ?> : <?php esc_html_e($app['date']); ?></p>
        </div>
    </div>
<?php } ?>
<?php } else { ?>
    <p><?php _e('No Job Applications Found'); ?></p>
<?php } ?>

</div>
<!-- Jobs Application List container end -->