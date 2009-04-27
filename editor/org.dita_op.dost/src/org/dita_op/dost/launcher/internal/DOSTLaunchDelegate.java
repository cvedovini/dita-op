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

import java.io.File;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import org.dita_op.dost.launcher.internal.ui.launchConfiguration.DOSTLaunchConfigurationConstants;
import org.eclipse.ant.core.AntCorePlugin;
import org.eclipse.ant.core.AntCorePreferences;
import org.eclipse.ant.internal.ui.launchConfigurations.AntLaunchDelegate;
import org.eclipse.ant.internal.ui.launchConfigurations.IAntLaunchConfigurationConstants;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IPath;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Path;
import org.eclipse.core.runtime.Status;
import org.eclipse.core.variables.VariablesPlugin;
import org.eclipse.debug.core.ILaunch;
import org.eclipse.debug.core.ILaunchConfiguration;
import org.eclipse.debug.core.ILaunchConfigurationWorkingCopy;
import org.eclipse.debug.core.model.LaunchConfigurationDelegate;
import org.eclipse.debug.internal.core.LaunchConfiguration;
import org.eclipse.debug.internal.core.LaunchConfigurationWorkingCopy;
import org.eclipse.jdt.launching.IJavaLaunchConfigurationConstants;
import org.eclipse.jdt.launching.IRuntimeClasspathEntry;
import org.eclipse.jdt.launching.JavaRuntime;
import org.eclipse.ui.externaltools.internal.model.IExternalToolConstants;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class DOSTLaunchDelegate extends LaunchConfigurationDelegate {

	private static final class TransientLaunchConfigurationWorkingCopy extends
			LaunchConfigurationWorkingCopy {

		public TransientLaunchConfigurationWorkingCopy(
				LaunchConfiguration original) throws CoreException {
			super(original);
		}

		/**
		 * @see org.eclipse.debug.internal.core.LaunchConfigurationWorkingCopy#doSave(org.eclipse.core.runtime.IProgressMonitor)
		 */
		@Override
		public synchronized ILaunchConfiguration doSave(IProgressMonitor monitor)
				throws CoreException {
			return this;
		}

	}

	private final AntLaunchDelegate antdelegate;

	public DOSTLaunchDelegate() {
		antdelegate = new AntLaunchDelegate();
	}

	@SuppressWarnings("unchecked")//$NON-NLS-1$
	public void launch(ILaunchConfiguration configuration, String mode,
			ILaunch launch, IProgressMonitor monitor) throws CoreException {
		ILaunchConfigurationWorkingCopy wc = new TransientLaunchConfigurationWorkingCopy(
				(LaunchConfiguration) configuration);

		Map antProperties = new HashMap();
		Map args = wc.getAttribute(DOSTLaunchConfigurationConstants.OTHER_ARGS,
				(Map) null);

		if (args != null) {
			antProperties.putAll(args);
		}

		File ditadir = new File(
				Activator.getDefault().getPreferenceStore().getString(
						DOSTParameters.DITA_DIR));

		File ditabuild = new File(ditadir, "build.xml"); //$NON-NLS-1$

		if (!ditabuild.exists() || !ditabuild.canRead()) {
			IStatus status = new Status(
					IStatus.ERROR,
					Activator.PLUGIN_ID,
					Messages.getString("DOSTLaunchDelegate.couldNotFindBuildScripts")); //$NON-NLS-1$
			throw new CoreException(status);
		}

		String ditamap = wc.getAttribute(DOSTParameters.ARGS_INPUT, ""); //$NON-NLS-1$
		ditamap = resolve(ditamap);

		String outdir = wc.getAttribute(DOSTParameters.OUTPUT_DIR, new File(
				ditamap).getParent());
		String transtype = wc.getAttribute(DOSTParameters.TRANSTYPE,
				DOSTParameters.DEFAULT_TRANSTYPE);

		File tempdir = new File(
				Activator.getDefault().getStateLocation().toFile(), "temp"); //$NON-NLS-1$

		antProperties.put(DOSTParameters.DITA_DIR, ditadir.getAbsolutePath());
		antProperties.put(DOSTParameters.ARGS_INPUT, ditamap);
		antProperties.put(DOSTParameters.OUTPUT_DIR, outdir);
		antProperties.put(DOSTParameters.TRANSTYPE, transtype);
		antProperties.put(DOSTParameters.DITA_TEMP_DIR,
				tempdir.getAbsolutePath());
		antProperties.put(DOSTParameters.CLEAN_TEMP, "yes"); //$NON-NLS-1$

		wc.setAttribute(IAntLaunchConfigurationConstants.ATTR_ANT_PROPERTIES,
				antProperties);
		wc.setAttribute(IExternalToolConstants.ATTR_LOCATION,
				ditabuild.getAbsolutePath());

		configureClasspath(ditadir, wc);

		antdelegate.launch(wc, mode, launch, monitor);
	}

	private String resolve(String value) throws CoreException {
		return VariablesPlugin.getDefault().getStringVariableManager().performStringSubstitution(
				value);
	}

	@SuppressWarnings("unchecked")//$NON-NLS-1$
	private void configureClasspath(File ditadir,
			ILaunchConfigurationWorkingCopy configuration) {
		IRuntimeClasspathEntry[] classpath = getFullClasspath(ditadir);

		configuration.setAttribute(
				IJavaLaunchConfigurationConstants.ATTR_CLASSPATH_PROVIDER,
				"org.eclipse.ant.ui.AntClasspathProvider"); //$NON-NLS-1$
		configuration.setAttribute(
				IJavaLaunchConfigurationConstants.ATTR_DEFAULT_CLASSPATH, false);
		try {
			List mementos = new ArrayList(classpath.length);

			for (int i = 0; i < classpath.length; i++) {
				IRuntimeClasspathEntry entry = classpath[i];
				mementos.add(entry.getMemento());
			}

			configuration.setAttribute(
					IJavaLaunchConfigurationConstants.ATTR_CLASSPATH, mementos);
		} catch (CoreException e) {
			Activator.getDefault().log(IStatus.ERROR, e);
		}

	}

	private IRuntimeClasspathEntry[] getFullClasspath(File ditadir) {
		List<IRuntimeClasspathEntry> fullClasspath = new ArrayList<IRuntimeClasspathEntry>();

		File ditalibfolder = new File(ditadir, "lib"); //$NON-NLS-1$
		List<File> ditalibs = getDOTKLibraries(ditalibfolder);

		IPath path = new Path(ditalibfolder.getAbsolutePath());
		fullClasspath.add(JavaRuntime.newArchiveRuntimeClasspathEntry(path));

		for (File ditalib : ditalibs) {
			path = new Path(ditalib.getAbsolutePath());
			fullClasspath.add(JavaRuntime.newArchiveRuntimeClasspathEntry(path));
		}

		// Add libraries from Ant preferences
		AntCorePreferences preferences = AntCorePlugin.getPlugin().getPreferences();

		for (URL url : preferences.getURLs()) {
			path = new Path(url.getPath());
			fullClasspath.add(JavaRuntime.newArchiveRuntimeClasspathEntry(path));
		}

		return fullClasspath.toArray(new IRuntimeClasspathEntry[0]);
	}

	private List<File> getDOTKLibraries(File ditadir) {
		return addLibsInFolder(ditadir, new ArrayList<File>());
	}

	private List<File> addLibsInFolder(File folder, List<File> libs) {
		File[] children = folder.listFiles();

		for (File child : children) {
			if (!child.isHidden()) {
				if (child.isFile()) {
					if (child.getName().endsWith(".jar")) { //$NON-NLS-1$
						libs.add(child);
					}
				} else {
					addLibsInFolder(child, libs);
				}
			}
		}

		return libs;
	}
}
