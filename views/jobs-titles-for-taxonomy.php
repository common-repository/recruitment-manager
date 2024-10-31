<?php $jobs = cwrm_getJobsTitles($data['sc_taxonomy'], $data['sc_term']); ?>

<?php if ($jobs) { ?>
<!-- Jobs Titles container start -->
<ul class="cwrm-job-titles-list cwrm-job-titles-list-<?php esc_attr_e($data['sc_taxonomy']); ?>"
    id="cwrm-job-titles-list-<?php esc_attr_e($data['sc_taxonomy']); ?>">
<?php foreach ($jobs as $job) { ?>
    <li class="cwrm-job-titles-list-item cwrm-job-titles-list-item-<?php esc_attr_e($job['id']); ?>">
        <a href="<?php echo esc_url($job['link']); ?>" target="_blank">
            <?php esc_html_e($job['title']); ?>
        </a>
    </li>
<?php } ?>
</ul>
<!-- Jobs Titles container end -->
<?php } ?>
