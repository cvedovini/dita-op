<?php
    define('WP_USE_THEMES', false);
    if ( !isset($wp_did_header) ) {
        $wp_did_header = true;
        require_once( '../../blog//wp-load.php' );
        wp('pagename=Documentation');
        get_header();
    }?>
<div class="content span-16" lang="en-us" xml:lang="en-us">
<a name="toolkit_preferences"><!-- --></a>


    <h1 class="topictitle1">Setting up the DITA Open Toolkit</h1>

    
    <div>
        <div class="section">
            To create DITA-OT launch configurations you first need to
            specify where the toolkit is installed.
        </div>

        <ol>
            <li>
                <span>
                    Select the
                    <span class="menucascade">
                        <span class="uicontrol">Window</span>
                         &gt; <span class="uicontrol">Preferences...</span>
                    </span>
                    menu
                </span>
            </li>

            <li>
                <span>
                    Select the
                    <span class="menucascade">
                        <span class="uicontrol">DITA</span>
                         &gt; <span class="uicontrol">Open Toolkit</span>
                    </span>
                    preference page
                </span>
            </li>

            <li>
                <span>
                    Click on the
                    <span class="uicontrol">Browse...</span>
                    button
                </span>
            </li>

            <li>
                <span>
                    Navigate the folder where your installation of the
                    DITA-OT is located
                </span>
            </li>

            <li>
                <span>
                    Click the
                    <span class="uicontrol">Open</span>
                    button
                </span>
            </li>

            <li>
                <span>
                    Click the
                    <span class="uicontrol">OK</span>
                    button
                </span>
            </li>

        </ol>

        <div class="section">
            You can now
            <a href="toolkit_launch_configuration.php">
                create a DITA-OT launch configuration.
            </a>
        </div>

    </div>

<div></div>


</div><?php
      get_sidebar();
      get_footer();
    ?>