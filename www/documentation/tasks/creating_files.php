<?php
      define('WP_USE_THEMES', false);
      require('../../blog//wp-blog-header.php');
    ?>
<?php
      get_header();
    ?>
<div class="content span-16" lang="en-us" xml:lang="en-us">
<a name="creating_files"><!-- --></a>


    <h1 class="topictitle1">Creating DITA Files</h1>

    
    <div>
        <ol>
            <li>
                <span>
                    Right-click on your DITA Project and select the
                    <span class="menucascade">
                        <span class="uicontrol">New</span>
                         &gt; <span class="uicontrol">Other...</span>
                    </span>
                    menu
                </span>
            </li>

            <li>
                <span>
                    In the
                    <span class="uicontrol">DITA</span>
                    category choose the type of DITA file you wish to
                    create
                </span>
            </li>

            <li>
                <span>
                    Click on the
                    <span class="uicontrol">Next</span>
                    button
                </span>
            </li>

            <li>
                <span>
                    Choose a
                    <kbd class="userinput">file name</kbd>
                </span>
            </li>

            <li>
                <span>
                    Click on the
                    <span class="uicontrol">Finish</span>
                    button
                </span>
            </li>

        </ol>

        <div class="section">
            The newly created file is automatically opened in the
            corresponding editor
        </div>

    </div>



</div><?php
      get_sidebar();
      get_footer();
    ?>