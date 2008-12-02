<?xml version="1.0" encoding="UTF-8" ?>
<!-- This file is part of the DITA Open Toolkit project hosted on 
     Sourceforge.net. See the accompanying license.txt file for 
     applicable licenses.-->
<!-- (c) Copyright IBM Corp. 2004, 2005 All Rights Reserved. -->

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:saxon="http://icl.com/saxon"
                extension-element-prefixes="saxon"
                >

<!-- map2htmltoc.xsl   main stylesheet
     Convert DITA map to a simple HTML table-of-contents view.
     Input = one DITA map file
     Output = one HTML file for viewing/checking by the author.

     Options:
        OUTEXT  = XHTML output extension (default is '.html')
        WORKDIR = The working directory that contains the document being transformed.
                   Needed as a directory prefix for the @href "document()" function calls.
                   Default is './'
-->

<!-- Include error message template -->
<xsl:import href="../../../xsl/common/output-message.xsl"/>

<xsl:output method="html" encoding="UTF-8" indent="no"/>

<!-- Set the prefix for error message numbers -->
<xsl:variable name="msgprefix">DOTX</xsl:variable>

<!-- *************************** Command line parameters *********************** -->
<xsl:param name="OUTEXT" select="'.html'"/><!-- "htm" and "html" are valid values -->
<xsl:param name="WORKDIR" select="'./'"/>
<xsl:param name="DITAEXT" select="'.xml'"/>
<xsl:param name="FILEREF" select="'file://'"/>
<xsl:param name="WPPATH" select="'./'"/>
<xsl:param name="WPPAGE" />
<xsl:param name="WPCONTENTCLASS" select="'widecolumn'"/>
<!-- the path back to the project. Used for c.gif, delta.gif, css to allow user's to have
  these files in 1 location. -->
<xsl:param name="PATH2PROJ">
  <xsl:apply-templates select="/processing-instruction('path2project')" mode="get-path2project"/>
</xsl:param>

<!-- Define a newline character -->
<xsl:variable name="newline"><xsl:text>
</xsl:text></xsl:variable>

<!-- Define the page title -->
<xsl:variable name="title">
  <xsl:choose>
      <xsl:when test="/*[contains(@class,' map/map ')]/*[contains(@class,' topic/title ')]">
        <xsl:value-of select="/*[contains(@class,' map/map ')]/*[contains(@class,' topic/title ')]"/>
      </xsl:when>
      <xsl:when test="/*[contains(@class,' map/map ')]/@title">
        <xsl:value-of select="/*[contains(@class,' map/map ')]/@title"/>
      </xsl:when>
    </xsl:choose>
</xsl:variable>

<xsl:template match="processing-instruction('path2project')" mode="get-path2project">
  <xsl:value-of select="."/>
</xsl:template>

<!-- *********************************************************************************
     Setup the HTML wrapper for the table of contents
     ********************************************************************************* -->
<xsl:template match="/">
    <xsl:text disable-output-escaping="yes"><![CDATA[<?php
        define('WP_USE_THEMES', false);
        require_once( ']]></xsl:text>
        <xsl:value-of select="concat($PATH2PROJ, $WPPATH, 'wp-load.php')" />
        <xsl:text disable-output-escaping="yes"><![CDATA[' );
        wp('pagename=]]></xsl:text>
        <xsl:value-of select="$WPPAGE" />
        <xsl:text disable-output-escaping="yes"><![CDATA[');
        get_header();
    ?>]]></xsl:text>
    <div id="content">
        <xsl:attribute name="class">
          <xsl:value-of select="$WPCONTENTCLASS"/>
        </xsl:attribute>
        <xsl:value-of select="$newline"/>
        <h1><xsl:value-of select="$title"/></h1>
        <xsl:value-of select="$newline"/>
        <xsl:apply-templates/>
    </div>
    <xsl:text disable-output-escaping="yes"><![CDATA[<?php
        get_sidebar();
        get_footer();
    ?>]]></xsl:text>
</xsl:template>

<!-- *********************************************************************************
     If processing only a single map, setup the HTML wrapper and output the contents.
     Otherwise, just process the contents.
     ********************************************************************************* -->
<xsl:template match="/*[contains(@class, ' map/map ')]">
  <xsl:param name="pathFromMaplist"/>
  <xsl:if test=".//*[contains(@class, ' map/topicref ')][not(@toc='no')]">
    <ul><xsl:value-of select="$newline"/>

      <xsl:apply-templates select="*[contains(@class, ' map/topicref ')]">
        <xsl:with-param name="pathFromMaplist" select="$pathFromMaplist"/>
      </xsl:apply-templates>
    </ul><xsl:value-of select="$newline"/>
  </xsl:if>
</xsl:template>
<!-- *********************************************************************************
     Output each topic as an <li> with an A-link. Each item takes 2 values:
     - A title. If a navtitle is specified on <topicref>, use that.
       Otherwise try to open the file and retrieve the title. First look for a navigation title in the topic,
       followed by the main topic title. Last, try to use <linktext> specified in the map.
       Failing that, use *** and issue a message.
     - An HREF is optional. If none is specified, this will generate a wrapper.
       Include the HREF if specified.
     - Ignore if TOC=no.

     If this topicref has any child topicref's that will be part of the navigation,
     output a <ul> around them and process the contents.
     ********************************************************************************* -->
<xsl:template match="*[contains(@class, ' map/topicref ')][not(@toc='no')]">
  <xsl:param name="pathFromMaplist"/>
  <xsl:variable name="title">
    <xsl:call-template name="navtitle"/>
  </xsl:variable>
  <xsl:choose>
    <xsl:when test="$title and $title!=''">
      <li>
        <xsl:choose>
          <!-- If there is a reference to a DITA or HTML file, and it is not external: -->
          <xsl:when test="@href and not(@href='')">
            <xsl:element name="a">
              <xsl:attribute name="href">
                <xsl:choose>        <!-- What if targeting a nested topic? Need to keep the ID? -->
                  <xsl:when test="contains(@copy-to, $DITAEXT)">
                    <xsl:if test="not(@scope='external')"><xsl:value-of select="$pathFromMaplist"/></xsl:if>
                    <xsl:value-of select="substring-before(@copy-to,$DITAEXT)"/><xsl:value-of select="$OUTEXT"/>
                  </xsl:when>
                  <xsl:when test="contains(@href,$DITAEXT)">
                    <xsl:if test="not(@scope='external')"><xsl:value-of select="$pathFromMaplist"/></xsl:if>
                    <xsl:value-of select="substring-before(@href,$DITAEXT)"/><xsl:value-of select="$OUTEXT"/>
                  </xsl:when>
                  <xsl:otherwise>  <!-- If non-DITA, keep the href as-is -->
                    <xsl:if test="not(@scope='external')"><xsl:value-of select="$pathFromMaplist"/></xsl:if>
                    <xsl:value-of select="@href"/>
                  </xsl:otherwise>
                </xsl:choose>
              </xsl:attribute>
              <xsl:if test="@scope='external' or @type='external' or ((@format='PDF' or @format='pdf') and not(@scope='local'))">
                <xsl:attribute name="target">_blank</xsl:attribute>
              </xsl:if>
              <xsl:value-of select="$title"/>
            </xsl:element>
          </xsl:when>
          
          <xsl:otherwise>
            <xsl:value-of select="$title"/>
          </xsl:otherwise>
        </xsl:choose>
        
        <!-- If there are any children that should be in the TOC, process them -->
        <xsl:if test="descendant::*[contains(@class, ' map/topicref ')][not(contains(@toc,'no'))]">
          <xsl:value-of select="$newline"/><ul><xsl:value-of select="$newline"/>
            <xsl:apply-templates select="*[contains(@class, ' map/topicref ')]">
              <xsl:with-param name="pathFromMaplist" select="$pathFromMaplist"/>
            </xsl:apply-templates>
          </ul><xsl:value-of select="$newline"/>
        </xsl:if>
      </li><xsl:value-of select="$newline"/>
    </xsl:when>
    <xsl:otherwise>
      <!-- if it is an empty topicref -->
      <xsl:apply-templates select="*[contains(@class, ' map/topicref ')]">
        <xsl:with-param name="pathFromMaplist" select="$pathFromMaplist"/>
      </xsl:apply-templates>
    </xsl:otherwise>
  </xsl:choose>
  
</xsl:template>

<!-- If toc=no, but a child has toc=yes, that child should bubble up to the top -->
<xsl:template match="*[contains(@class, ' map/topicref ')][@toc='no']">
  <xsl:param name="pathFromMaplist"/>
  <xsl:apply-templates select="*[contains(@class, ' map/topicref ')]">
    <xsl:with-param name="pathFromMaplist" select="$pathFromMaplist"/>
  </xsl:apply-templates>
</xsl:template>

<xsl:template match="processing-instruction('workdir')" mode="get-work-dir">
  <xsl:value-of select="."/><xsl:text>/</xsl:text>
</xsl:template>  

<xsl:template name="navtitle">
  <xsl:variable name="WORKDIR">
    <xsl:value-of select="$FILEREF"/>
    <xsl:apply-templates select="/processing-instruction()" mode="get-work-dir"/>
  </xsl:variable>
  <xsl:choose>

    <!-- If navtitle is specified, use it (!?but it should only be used when locktitle=yes is specifed?!) -->
    <xsl:when test="@navtitle"><xsl:value-of select="@navtitle"/></xsl:when>

    <!-- If this references a DITA file (has @href, not "local" or "external"),
         try to open the file and get the title -->
    <xsl:when test="@href and not(@href='') and 
                    not ((ancestor-or-self::*/@scope)[last()]='external') and
                    not ((ancestor-or-self::*/@scope)[last()]='peer') and
                    not ((ancestor-or-self::*/@type)[last()]='external') and
                    not ((ancestor-or-self::*/@type)[last()]='local')">
      <!-- Need to worry about targeting a nested topic? Not for now. -->
      <!--<xsl:variable name="FileWithPath"><xsl:value-of select="$WORKDIR"/><xsl:choose>-->
      <xsl:variable name="FileWithPath"><xsl:choose>
        <xsl:when test="@copy-to"><xsl:value-of select="$WORKDIR"/><xsl:value-of select="@copy-to"/></xsl:when>
        <xsl:when test="contains(@href,'#')"><xsl:value-of select="$WORKDIR"/><xsl:value-of select="substring-before(@href,'#')"/></xsl:when>
        <xsl:otherwise><xsl:value-of select="$WORKDIR"/><xsl:value-of select="@href"/></xsl:otherwise></xsl:choose></xsl:variable>
      <xsl:variable name="TargetFile" select="document($FileWithPath,/)"/>

      <xsl:choose>
        <xsl:when test="not($TargetFile)">   <!-- DITA file does not exist -->
          <xsl:choose>
            <xsl:when test="*[contains(@class, ' map/topicmeta ')]/*[contains(@class, ' map/linktext ')]">  <!-- attempt to recover by using linktext -->
              <xsl:value-of select="*[contains(@class, ' map/topicmeta ')]/*[contains(@class, ' map/linktext ')]"/>
            </xsl:when>
            <xsl:otherwise>
              <xsl:call-template name="output-message">
                <xsl:with-param name="msgnum">008</xsl:with-param>
                <xsl:with-param name="msgsev">W</xsl:with-param>
                <xsl:with-param name="msgparams">%1=<xsl:value-of select="@href"/></xsl:with-param>
               </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <!-- First choice for navtitle: topic/titlealts/navtitle -->
        <xsl:when test="$TargetFile/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/titlealts ')]/*[contains(@class,' topic/navtitle ')]">
          <xsl:value-of select="$TargetFile/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/titlealts ')]/*[contains(@class,' topic/navtitle ')]"/>
        </xsl:when>
        <!-- Second choice for navtitle: topic/title -->
        <xsl:when test="$TargetFile/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/title ')]">
          <xsl:value-of select="$TargetFile/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/title ')]"/>
        </xsl:when>
        <!-- This might be a combo article; modify the same queries: dita/topic/titlealts/navtitle -->
        <xsl:when test="$TargetFile/dita/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/titlealts ')]/*[contains(@class,' topic/navtitle ')]">
          <xsl:value-of select="$TargetFile/dita/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/titlealts ')]/*[contains(@class,' topic/navtitle ')]"/>
        </xsl:when>
        <!-- Second choice: dita/topic/title -->
        <xsl:when test="$TargetFile/dita/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/title ')]">
          <xsl:value-of select="$TargetFile/dita/*[contains(@class,' topic/topic ')]/*[contains(@class,' topic/title ')]"/>
        </xsl:when>
        <!-- Last choice: use the linktext specified within the topicref -->
        <xsl:when test="*[contains(@class, ' map/topicmeta ')]/*[contains(@class, ' map/linktext ')]">
          <xsl:value-of select="*[contains(@class, ' map/topicmeta ')]/*[contains(@class, ' map/linktext ')]"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:call-template name="output-message">
            <xsl:with-param name="msgnum">009</xsl:with-param>
            <xsl:with-param name="msgsev">W</xsl:with-param>
            <xsl:with-param name="msgparams">%1=<xsl:value-of select="@TargetFile"/>;%2=***</xsl:with-param>
          </xsl:call-template>
          <xsl:text>***</xsl:text>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:when>

    <!-- If there is no title and none can be retrieved, check for <linktext> -->
    <xsl:when test="*[contains(@class, ' map/topicmeta ')]/*[contains(@class, ' map/linktext ')]">
      <xsl:value-of select="*[contains(@class, ' map/topicmeta ')]/*[contains(@class, ' map/linktext ')]"/>
    </xsl:when>

    <!-- No local title, and not targeting a DITA file. Could be just a container setting
         metadata, or a file reference with no title. Issue message for the second case. -->
    <xsl:otherwise>
      <xsl:if test="@href and not(@href='')">
          <xsl:call-template name="output-message">
            <xsl:with-param name="msgnum">009</xsl:with-param>
            <xsl:with-param name="msgsev">W</xsl:with-param>
            <xsl:with-param name="msgparams">%1=<xsl:value-of select="@href"/>;%2=<xsl:value-of select="@href"/></xsl:with-param>
          </xsl:call-template>
          <xsl:value-of select="@href"/>
      </xsl:if>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!-- To be overridden by user shell. -->
<xsl:template name="gen-user-head"/>
<xsl:template name="gen-user-scripts"/>
<xsl:template name="gen-user-styles"/>

<!-- These are here just to prevent accidental fallthrough -->
<xsl:template match="*[contains(@class, ' map/navref ')]"/>
<xsl:template match="*[contains(@class, ' map/anchor ')]"/>
<xsl:template match="*[contains(@class, ' map/reltable ')]"/>
<xsl:template match="*[contains(@class, ' map/topicmeta ')]"/>
<!--xsl:template match="*[contains(@class, ' map/topicref ') and contains(@class, '/topicgroup ')]"/-->

<xsl:template match="*">
  <xsl:apply-templates/>
</xsl:template>

<!-- Convert the input value to lowercase & return it -->
<xsl:template name="convert-to-lower">
 <xsl:param name="inputval"/>
 <xsl:value-of select="translate($inputval,
                                  '-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                                  '-abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz')"/>
</xsl:template>

<!-- Template to get the relative path to a map -->
<xsl:template name="getRelativePath">
  <xsl:param name="remainingPath" select="@file"/>
  <xsl:choose>
    <xsl:when test="contains($remainingPath,'/')">
      <xsl:value-of select="substring-before($remainingPath,'/')"/><xsl:text>/</xsl:text>
      <xsl:call-template name="getRelativePath">
        <xsl:with-param name="remainingPath" select="substring-after($remainingPath,'/')"/>
      </xsl:call-template>
    </xsl:when>
    <xsl:when test="contains($remainingPath,'\')">
      <xsl:value-of select="substring-before($remainingPath,'\')"/><xsl:text>/</xsl:text>
      <xsl:call-template name="getRelativePath">
        <xsl:with-param name="remainingPath" select="substring-after($remainingPath,'\')"/>
      </xsl:call-template>
    </xsl:when>
  </xsl:choose>
</xsl:template>

</xsl:stylesheet>
