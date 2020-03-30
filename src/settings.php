<?php

add_action('admin_menu', 'rewrite_media_to_s3_settings_page');
add_action('admin_init', 'rewrite_media_to_s3_settings_init');

function rewrite_media_to_s3_settings_page()
{
    add_options_page(
        'Rewrite Media to S3',
        'Rewrite Media to S3',
        'manage_options',
        'rewrite-media-to-s3',
        'rewrite_media_to_s3'
    );
}

function rewrite_media_to_s3_settings_init()
{
    register_setting('rewrite_media_to_s3_plugin', 'rewrite_media_to_s3_settings');
    add_settings_section(
        'rewrite_media_to_s3_settings_section',
        __('URL Signatures', 'wordpress'),
        'rewrite_media_to_s3_signature_section_intro',
        'rewrite_media_to_s3_plugin'
    );

    add_settings_field(
        'rewrite_media_to_s3_select_expire',
        __('Expiry Time:', 'wordpress'),
        'rewrite_media_to_s3_select_field_2_render',
        'rewrite_media_to_s3_plugin',
        'rewrite_media_to_s3_settings_section'
    );

    add_settings_field(
        'rewrite_media_to_s3_exclude',
        __('Exclude CPT', 'wordpress'),
        'rewrite_media_to_s3_text_field_1_render',
        'rewrite_media_to_s3_plugin',
        'rewrite_media_to_s3_settings_section'
    );

    add_settings_field(
        'rewrite_media_to_s3_select',
        __('Activation', 'wordpress'),
        'rewrite_media_to_s3_select_field_1_render',
        'rewrite_media_to_s3_plugin',
        'rewrite_media_to_s3_settings_section'
    );
}

function rewrite_media_to_s3_text_field_1_render()
{
    $options = get_option('rewrite_media_to_s3_settings');
    ?>
    <input type="text" value="<?= $options['exclusion_list_of_cpt'] ?: '' ?>" placeholder="separate with a comma" name='rewrite_media_to_s3_settings[exclusion_list_of_cpt]'>
    <p>Enter custom post types separated by comma to exclude from url signing.</p>
    <?php
}

function rewrite_media_to_s3_select_field_1_render()
{
    $options = get_option('rewrite_media_to_s3_settings');
    ?>
    <select name='rewrite_media_to_s3_settings[create_secure_urls_select]'>
        <option value='' disabled="disabled">Create secure URLs?</option>
        <option value='no' <?php selected($options['create_secure_urls_select'], 'no'); ?>>No</option>
        <option value='yes' <?php selected($options['create_secure_urls_select'], 'yes'); ?>>Yes</option>
    </select>

    <?php
}

function rewrite_media_to_s3_select_field_2_render()
{
    $options = get_option('rewrite_media_to_s3_settings');

    $option_values = [
        'option_1' => '30 seconds',
        'option_2' => '1 minute',
        'option_3' => '5 minutes',
        'option_4' => '10 minutes',
        'option_5' => '20 minutes',
        'option_6' => '30 minutes'
    ];
    ?>
    <select name='rewrite_media_to_s3_settings[secure_expiry_time]'>
        <option value='' disabled="disabled">URLs expire after:</option>
        <?php
        foreach ($option_values as $value) {
            echo "<option value='" . $value . "' " . selected($options['secure_expiry_time'], $value) . ">" . $value . "</option>";
        }
        ?>
    </select>
    <p>Define the 'end of life' time for a media URL. The time starts once a request for the media has been made by a browser.<br>Once time has expired the media on S3 will no longer be accessible.</p>
    <?php
}

function rewrite_media_to_s3_signature_section_intro()
{

    echo __('<strong>The AWS SDK is required to sign urls.</strong><br>The settings in this section define behaviour for the url signature feature provided by AWS S3.', 'wordpress');
}

function rewrite_media_to_s3()
{
    ?>
    <form action='options.php' method='post'>

        <h1>Rewrite Media to S3 <small style="color:#aaaaaa">. admin page</small></h1>

        <?php
        settings_fields('rewrite_media_to_s3_plugin');
        do_settings_sections('rewrite_media_to_s3_plugin');
        submit_button();
        ?>

    </form>
    <?php
}
