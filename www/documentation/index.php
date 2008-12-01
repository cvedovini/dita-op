<?php
      define('WP_USE_THEMES', false);
      require('../blog//wp-blog-header.php');
    ?>
<?php
      get_header();
    ?>
<div class="content span-16">
<h1>DITA Open Platform User Guide</h1>
<ul>
<li><a href="topics/about.php">About</a></li>
<li>Getting started
<ul>
<li><a href="tasks/creating_project.php">Creating a DITA Project</a></li>
<li><a href="tasks/creating_files.php">Creating DITA Files</a></li>
<li><a href="tasks/toolkit_preferences.php">Setting up the DITA Open Toolkit</a></li>
<li><a href="tasks/toolkit_launch_configuration.php">Creating a DITA Open Toolkit launch configuration</a></li>
</ul>
</li>
<li>Tasks
<ul>
<li><a href="tasks/creating_project.php">Creating a DITA Project</a></li>
<li><a href="tasks/creating_files.php">Creating DITA Files</a></li>
<li><a href="tasks/toolkit_preferences.php">Setting up the DITA Open Toolkit</a></li>
<li><a href="tasks/toolkit_launch_configuration.php">Creating a DITA Open Toolkit launch configuration</a></li>
</ul>
</li>
</ul>
</div><?php
      get_sidebar();
      get_footer();
    ?>