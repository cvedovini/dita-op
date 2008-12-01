<?php
      define('WP_USE_THEMES', false);
      require('../../blog//wp-blog-header.php');
    ?>
<?php
      get_header();
    ?>
<div class="content span-16" lang="en-us" xml:lang="en-us">
<a name="creating_project"><!-- --></a>


    <h1 class="topictitle1">Creating a DITA Project</h1>

    
    <div>
        <ol>
            <li>
                <span>
                    Select the
                    <span class="menucascade">
                        <span class="uicontrol">File</span>
                         &gt; <span class="uicontrol">New</span>
                         &gt; <span class="uicontrol">Project...</span>
                    </span>
                    menu
                </span>
            </li>

            <li>
                <span>
                    In the
                    <span class="uicontrol">DITA</span>
                    category choose
                    <span class="uicontrol">DITA Project</span>
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
                    Enter your
                    <kbd class="userinput">project name</kbd>
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
            <p>
                You can then import existing DITA files from your file
                system,
                <a href="http://help.eclipse.org/help33/topic/org.eclipse.platform.doc.user/gettingStarted/qs-31a.htm" target="_blank">
                    using the file import wizard or drag and dropping
                </a>
                them from your Windows Explorer window into your DITA
                project.
            </p>


            <p>
                You can also start
                <a href="creating_files.php">
                    creating new DITA Files
                </a>
                into your DITA Project.
            </p>

        </div>

    </div>

<div></div>


</div><?php
      get_sidebar();
      get_footer();
    ?>