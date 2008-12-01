<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:saxon="http://icl.com/saxon" extension-element-prefixes="saxon">

<xsl:import href="xslwp/dita2wpImpl.xsl"></xsl:import>
<xsl:import href="xslwp/taskdisplay.xsl"></xsl:import>
<xsl:import href="xslwp/refdisplay.xsl"></xsl:import>
<xsl:import href="xslwp/ut-d.xsl"></xsl:import>
<xsl:import href="xslwp/sw-d.xsl"></xsl:import>
<xsl:import href="xslwp/pr-d.xsl"></xsl:import>
<xsl:import href="xslwp/ui-d.xsl"></xsl:import>
<xsl:import href="xslwp/hi-d.xsl"></xsl:import>

<xsl:output method="html" encoding="UTF-8" indent="no" />


<xsl:param name="DITAEXT" select="'.xml'"></xsl:param>

<xsl:template match="/">
  <xsl:apply-templates></xsl:apply-templates>
</xsl:template>

</xsl:stylesheet>