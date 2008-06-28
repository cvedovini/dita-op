/**
 * Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.
 * 
 * This file is part of the DITA Open Platform <http://www.dita-op.org/>.
 * 
 * The DITA Open Platform is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 * 
 * The DITA Open Platform is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * The DITA Open Platform. If not, see <http://www.gnu.org/licenses/>.
 */
package org.dita_op.dost.launcher.internal;

/**
 * Constant definitions for DITA-OT standard parameters.
 * 
 * See <a
 * href="http://dita-ot.sourceforge.net/doc/ot-userguide/xhtml/processing/antparms.html">the
 * DITA Open Toolkit documentation</a> for more information.
 */
public interface DOSTParameters {

	/** The default transtype to use when none is specified */
	public static final String DEFAULT_TRANSTYPE = "xhtml"; //$NON-NLS-1$	

	/** Absolute path of the Toolkit's home directory. */
	public static final String DITA_DIR = "dita.dir"; //$NON-NLS-1$

	/** Path and name of the input file. */
	public static final String ARGS_INPUT = "args.input"; //$NON-NLS-1$

	/** Type of output to be produced. */
	public static final String TRANSTYPE = "transtype"; //$NON-NLS-1$

	/** Path of the output directory. */
	public static final String OUTPUT_DIR = "output.dir"; //$NON-NLS-1$

	/** Directory for the temporary files generated during the build. */
	public static final String DITA_TEMP_DIR = "dita.temp.dir"; //$NON-NLS-1$

	/** Whether to clean the temp directory before each build. */
	public static final String CLEAN_TEMP = "clean.temp"; //$NON-NLS-1$

	/**
	 * Name of the ditaval file that contains filter/flagging/revision
	 * information.
	 */
	public static final String DITA_INPUT_VALFILE = "dita.input.valfile"; //$NON-NLS-1$

	/** Should input files be validated. */
	public static final String VALIDATE = "validate"; //$NON-NLS-1$

	/**
	 * Include draft and required cleanup content (that is, items identified as
	 * left to do before publishing).
	 */
	public static final String ARGS_DRAFT = "args.draft"; //$NON-NLS-1$

	/**
	 * Directory used to store generated Ant log files. If you generate several
	 * outputs in a single build, the following rules apply:
	 * <ul>
	 * <li>If you specified a common logdir for all transformations, it will be
	 * used as the log directory.</li>
	 * <li>If you did not specify a common logdir for all transformations:
	 * <ul>
	 * <li>If all individual transforms have the same output directory, it will
	 * be used as the log directory.</li>
	 * <li>If all individual transforms do not have the same output directory,
	 * basedir will be used as the log directory.</li>
	 * </ul>
	 * </li>
	 * </ul>
	 */
	public static final String ARGS_LOGDIR = "args.logdir"; //$NON-NLS-1$

	/**
	 * File extension of the DITA source files.
	 * 
	 * <p>
	 * If you use extensions other than the default or the one you specify with
	 * this processing option (for example, .ditamap) you must specify the
	 * format attribute (for example, format="ditamap") in your source file
	 * references. If you don't, you will get an error message.
	 * </p>
	 */
	public static final String DITA_EXTNAME = "dita.extname"; //$NON-NLS-1$

	/** Should files not referenced in ditamap be resolved. */
	public static final String ONLYTOPIC_IN_MAP = "onlytopic.in.map"; //$NON-NLS-1$

	/**
	 * Adds annotation to images showing the filename of the image. Useful for
	 * pre-publishing editing.
	 */
	public static final String ARGS_ARTLBL = "args.artlbl"; //$NON-NLS-1$

	/**
	 * Whether to copy user-specified ARGS_CSS file(s) to the directory
	 * specified {args.outdir}${args.csspath}.
	 */
	public static final String ARGS_COPYCSS = "args.copycss"; //$NON-NLS-1$

	/** Name of user-specified ARGS_CSS file. Local or remote (web) file. */
	public static final String ARGS_CSS = "args.css"; //$NON-NLS-1$

	/**
	 * Path to user-specified CSS file.
	 * 
	 * <p>
	 * Notes:
	 * <ul>
	 * <li>If ${args.csspath} is a URL, it must start with http:// or https://.</li>
	 * <li>Local absolute paths are not supported for args.csspath.</li>
	 * <li>Use "/" as the path separator, and do not append a "/" trailing
	 * separator (for example, use css/mycssfiles rather than css/mycssfiles/).</li>
	 * </ul>
	 * </p>
	 * 
	 */
	public static final String ARGS_CSSPATH = "args.csspath"; //$NON-NLS-1$

	/** Locale used for sorting indexterms. */
	public static final String ARGS_DITA_LOCALE = "args.dita.locale"; //$NON-NLS-1$

	/** Whether to generate plugin files or not */
	public static final String DITA_ECLIPSE_PLUGIN = "dita.eclipse.plugin"; //$NON-NLS-1$

	/** Root file name of the output Eclipse content toc file. */
	public static final String ARGS_ECLIPSECONTENT_TOC = "args.eclipsecontent.toc"; //$NON-NLS-1$

	/** Root file name of the output Eclipse help toc file. */
	public static final String ARGS_ECLIPSEHELP_TOC = "args.eclipsehelp.toc"; //$NON-NLS-1$

	/** Provider name of the Eclipse help output. */
	public static final String ARGS_ECLIPSE_PROVIDER = "args.eclipse.provider"; //$NON-NLS-1$

	/** Version number of the Eclipse help output. */
	public static final String ARGS_ECLIPSE_VERSION = "args.eclipse.version"; //$NON-NLS-1$

	/** Extension name of the image files in the PDF output */
	public static final String ARGS_FO_IMG_EXT = "args.fo.img.ext"; //$NON-NLS-1$

	/** Whether links will appear in the output files. */
	public static final String ARGS_FO_OUTPUT_REL_LINKS = "args.fo.output.rel.links"; //$NON-NLS-1$

	/** Name of the configuration file for FOP processing. */
	public static final String ARGS_FO_USERCONFIG = "args.fo.userconfig"; //$NON-NLS-1$

	/**
	 * Path to the file containing XHTML to be placed in the body running-footer
	 * area of the output file.
	 */
	public static final String ARGS_FTR = "args.ftr"; //$NON-NLS-1$

	/**
	 * Path to the file containing XHTML to be placed in the header area of the
	 * output file.
	 */
	public static final String ARGS_HDF = "args.hdf"; //$NON-NLS-1$

	/**
	 * Path to the file containing XHTML to be placed in the body running-header
	 * area of the output file.
	 */
	public static final String ARGS_HDR = "args.hdr"; //$NON-NLS-1$

	/**
	 * Whether indexterm entries should display in the output text. Makes it
	 * possible to see what has been indexed in a pre-publishing review.
	 */
	public static final String ARGS_INDEXSHOW = "args.indexshow"; //$NON-NLS-1$

	/** xsl transform file that will replace the default file. */
	public static final String ARGS_XSL = "args.xsl"; //$NON-NLS-1$

}
