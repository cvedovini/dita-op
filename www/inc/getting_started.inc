<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<div class="nested0" xml:lang="en-US" id="N20006"><a name="N20006"><!-- --></a><h1 class="topictitle1">Creating a DITA Project</h1>
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
                <a href="#N60006">
                    creating new DITA Files
                </a>
                into your DITA Project.
            </p>

        </div>

    </div>
</div>
<div class="nested0" xml:lang="en-US" id="N60006"><a name="N60006"><!-- --></a><h1 class="topictitle1">Creating DITA Files</h1>
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
</div>
<div class="nested0" xml:lang="en-US" id="N70006"><a name="N70006"><!-- --></a><h1 class="topictitle1">Setting up the DITA Open Toolkit</h1>
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
            <a href="#N80006">
                create a DITA-OT launch configuration.
            </a>
        </div>

    </div>
</div>
<div class="nested0" xml:lang="en-US" id="N80006"><a name="N80006"><!-- --></a><h1 class="topictitle1">Creating a DITA Open Toolkit launch configuration</h1>
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
</div>
