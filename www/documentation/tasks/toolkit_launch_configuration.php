<?php
      define('WP_USE_THEMES', false);
      require('../../blog//wp-blog-header.php');
    ?>
<?php
      get_header();
    ?>
<div class="content span-16" lang="en-us" xml:lang="en-us">
<a name="toolkit_launch_configuration"><!-- --></a>


    <h1 class="topictitle1">Creating a DITA Open Toolkit launch configuration</h1>

    
    <div>
        <ol>
            <li>
                <span>
                    Select the
                    <span class="menucascade">
                        <span class="uicontrol">Run</span>
                         &gt; <span class="uicontrol">External Tools</span>
                         &gt; <span class="uicontrol">
                            Open External Tools Dialog...
                        </span>
                    </span>
                    menu
                </span>
            </li>

            <li>
                <span>
                    Right-click on the
                    <span class="uicontrol">DITA-OT Build</span>
                    category
                </span>
            </li>

            <li>
                <span>
                    Select the
                    <span class="uicontrol">New</span>
                    menu
                </span>
            </li>

            <li>
                <span>
                    Enter a
                    <kbd class="userinput">name</kbd>
                    for your configuration
                </span>
            </li>

            <li>
                <span>
                    Enter a
                    <kbd class="userinput">transformation type</kbd>
                    (xhtml, pdf, etc.)
                </span>
            </li>

            <li>
                <span>
                    Enter the
                    <kbd class="userinput">location of the DITA Map</kbd>
                    you wish to process
                </span>
            </li>

            <li>
                <span>
                    Enter the
                    <kbd class="userinput">
                        location for the generated output
                    </kbd>
                </span>
            </li>

            <li><strong>Optional: </strong>
                <span>
                    Enter the
                    <kbd class="userinput">
                        location of a ditaval processing profile
                    </kbd>
                </span>
            </li>

            <li><strong>Optional: </strong>
                <span>
                    Provide additional processing arguments
                </span>
            </li>

            <li>
                <span>
                    Click on the
                    <span class="uicontrol">Run</span>
                    button
                </span>
            </li>

        </ol>

        <div class="section">
            Once you ran it once, the configuration is available either
            under the
            <span class="menucascade">
                <span class="uicontrol">Run</span>
                 &gt; <span class="uicontrol">External Tools</span>
            </span>
            menu or with the
            <span class="uicontrol">Run External Tools</span>
            button in the tool bar.
        </div>

    </div>



</div><?php
      get_sidebar();
      get_footer();
    ?>