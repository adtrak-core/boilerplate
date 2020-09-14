<?php defined('SLICKPLAN_PLUGIN_PATH') or exit('No direct access allowed'); ?>

<?php $is_summary = (isset($xml['summary']) and $xml['summary']); ?>

<?php if (isset($xml['sitemap']) and is_array($xml['sitemap'])) { ?>
    <script type="text/javascript">
        window.SLICKPLAN_JSON = <?php echo json_encode($xml['sitemap']); ?>;
    </script>
<?php } ?>

<form action="" method="post" id="slickplan-importer">
    <h3><?php echo $is_summary ? 'Success!' : 'Importing Pages&hellip;'; ?></h3>
    <div class="updated slickplan-show-summary"<?php if (!$is_summary) { ?> style="display: none"<?php } ?>>
        <p>Pages have been imported. Thank you for using <a href="http://slickplan.com/" target="_blank">Slickplan</a> Importer.</p>
    </div>
    <?php if (!$is_summary) { ?>
        <div id="slickplan-progressbar" class="progressbar"><div class="progress-label">0%</div></div>
    <?php } ?>
    <hr>
    <div class="slickplan-summary"><?php if ($is_summary) echo $xml['summary']; ?></div>
    <hr>
    <p class="slickplan-show-summary"<?php if (!$is_summary) { ?> style="display: none"<?php } ?>>
        <a href="<?php echo esc_url(get_admin_url(null, 'edit.php?post_type=page')); ?>" class="button button-secondary">See all pages</a>
    </p>
</form>