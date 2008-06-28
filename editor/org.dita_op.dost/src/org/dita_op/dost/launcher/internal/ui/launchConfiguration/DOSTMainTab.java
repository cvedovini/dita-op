/**
 *  Copyright (C) 2008 Claude Vedovini <http://vedovini.net/>.
 *
 *  This file is part of the DITA Open Platform <http://www.dita-op.org/>.
 *
 *  The DITA Open Platform is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The DITA Open Platform is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with The DITA Open Platform.  If not, see <http://www.gnu.org/licenses/>.
 */
package org.dita_op.dost.launcher.internal.ui.launchConfiguration;

import java.io.File;
import java.util.HashMap;
import java.util.Map;

import org.dita_op.dost.launcher.internal.Activator;
import org.dita_op.dost.launcher.internal.DOSTParameters;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.variables.IStringVariableManager;
import org.eclipse.core.variables.VariablesPlugin;
import org.eclipse.debug.core.ILaunchConfiguration;
import org.eclipse.debug.core.ILaunchConfigurationWorkingCopy;
import org.eclipse.debug.ui.AbstractLaunchConfigurationTab;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Label;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.externaltools.internal.model.ExternalToolsImages;
import org.eclipse.ui.externaltools.internal.model.IExternalToolConstants;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class DOSTMainTab extends AbstractLaunchConfigurationTab {
	private final static String FIRST_EDIT = "editedByDOSTMainTab"; //$NON-NLS-1$

	private Text transtypeText;

	private Text ditamapText;
	private MultiBrowseButton ditamapBrowseButton;

	private Text outputText;
	private MultiBrowseButton outputBrowseButton;

	private Text profileText;
	private MultiBrowseButton profileBrowseButton;

	private DOSTArgumentsGroup argsGroup;

	private boolean initializing = false;
	private boolean userEdited = false;

	private ModifyListener listener = new ModifyListener() {
		public void modifyText(ModifyEvent e) {
			setDirty(true);
		}

	};

	public void setDirty(boolean dirty) {
		if (!initializing) {
			super.setDirty(true);
			userEdited = true;
			updateLaunchConfigurationDialog();
		}
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#createControl(org.eclipse.swt.widgets.Composite)
	 */
	public void createControl(Composite parent) {
		parent.setLayout(new GridLayout());

		Composite mainContainer = new Composite(parent, SWT.NONE);
		mainContainer.setLayout(new GridLayout());

		Composite container = new Composite(mainContainer, SWT.NONE);
		container.setLayout(new GridLayout(3, false));
		container.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		createTranstypeComponent(container);
		createDitamapComponent(container);
		createOutputComponent(container);
		createProfileComponent(container);

		createSeparator(mainContainer, 1);
		createArgsGroup(mainContainer);

		setControl(mainContainer);
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#setDefaults(org.eclipse.debug.core.ILaunchConfigurationWorkingCopy)
	 */
	@SuppressWarnings("unchecked")
	public void setDefaults(ILaunchConfigurationWorkingCopy configuration) {
		configuration.setAttribute(FIRST_EDIT, true);
		configuration.setAttribute(DOSTParameters.TRANSTYPE,
				DOSTParameters.DEFAULT_TRANSTYPE);

		Map defaultArgs = new HashMap();

		defaultArgs.put(DOSTParameters.DITA_EXTNAME, ".dita"); //$NON-NLS-1$
		defaultArgs.put(DOSTParameters.ARGS_CSSPATH, "css"); //$NON-NLS-1$
		defaultArgs.put(DOSTParameters.ARGS_ECLIPSEHELP_TOC, "toc"); //$NON-NLS-1$
		defaultArgs.put(DOSTParameters.ARGS_ECLIPSECONTENT_TOC, "toc"); //$NON-NLS-1$
		defaultArgs.put(DOSTParameters.ARGS_ECLIPSE_PROVIDER, "dita-op.org"); //$NON-NLS-1$
		defaultArgs.put(DOSTParameters.ARGS_ECLIPSE_VERSION, "1.0.0.qualifier"); //$NON-NLS-1$

		configuration.setAttribute(DOSTLaunchConfigurationConstants.OTHER_ARGS,
				defaultArgs);
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#initializeFrom(org.eclipse.debug.core.ILaunchConfiguration)
	 */
	public void initializeFrom(ILaunchConfiguration configuration) {
		initializing = true;
		updateDitamap(configuration);
		updateProfile(configuration);
		updateTranstype(configuration);
		updateOutput(configuration);
		updateArguments(configuration);
		initializing = false;
		setDirty(false);
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#performApply(org.eclipse.debug.core.ILaunchConfigurationWorkingCopy)
	 */
	@SuppressWarnings("unchecked")
	public void performApply(ILaunchConfigurationWorkingCopy configuration) {
		String ditamap = ditamapText.getText().trim();

		if (ditamap.length() == 0) {
			configuration.setAttribute(DOSTParameters.ARGS_INPUT, (String) null);
		} else {
			configuration.setAttribute(DOSTParameters.ARGS_INPUT, ditamap);
		}

		String profile = profileText.getText().trim();

		if (profile.length() == 0) {
			configuration.setAttribute(DOSTParameters.DITA_INPUT_VALFILE,
					(String) null);
		} else {
			configuration.setAttribute(DOSTParameters.DITA_INPUT_VALFILE,
					profile);
		}

		String transtype = transtypeText.getText().trim();

		if (transtype.length() == 0) {
			configuration.setAttribute(DOSTParameters.TRANSTYPE, (String) null);
		} else {
			configuration.setAttribute(DOSTParameters.TRANSTYPE, transtype);
		}

		String output = outputText.getText().trim();

		if (output.length() == 0) {
			configuration.setAttribute(DOSTParameters.OUTPUT_DIR, (String) null);
		} else {
			configuration.setAttribute(DOSTParameters.OUTPUT_DIR, output);
		}

		Map args = argsGroup.getArguments();
		configuration.setAttribute(DOSTLaunchConfigurationConstants.OTHER_ARGS,
				args);

		if (userEdited) {
			configuration.setAttribute(FIRST_EDIT, (String) null);
		}
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#getName()
	 */
	public String getName() {
		return Messages.getString("DOSTMainTab.title"); //$NON-NLS-1$
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#isValid(org.eclipse.debug.core.ILaunchConfiguration)
	 */
	@Override
	public boolean isValid(ILaunchConfiguration launchConfig) {
		setErrorMessage(null);
		setMessage(null);
		boolean newConfig = false;

		try {
			newConfig = launchConfig.getAttribute(FIRST_EDIT, false);
		} catch (CoreException e) {
			// assume false is correct
		}

		return validateDitamap(newConfig) && validateTranstype()
				&& validateProfile() && validateOutput(newConfig);
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#getImage()
	 */
	@Override
	public Image getImage() {
		return ExternalToolsImages.getImage(IExternalToolConstants.IMG_TAB_MAIN);
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#deactivated(org.eclipse.debug.core.ILaunchConfigurationWorkingCopy)
	 */
	@Override
	public void deactivated(ILaunchConfigurationWorkingCopy workingCopy) {
	}

	/**
	 * @see org.eclipse.debug.ui.ILaunchConfigurationTab#activated(org.eclipse.debug.core.ILaunchConfigurationWorkingCopy)
	 */
	@Override
	public void activated(ILaunchConfigurationWorkingCopy workingCopy) {
	}

	private void createArgsGroup(Composite parent) {
		argsGroup = new DOSTArgumentsGroup(parent) {

			@Override
			public void setDirty(boolean dirty) {
				DOSTMainTab.this.setDirty(dirty);
			}

		};
	}

	private void createDitamapComponent(Composite parent) {
		Label label = new Label(parent, SWT.NONE);
		label.setText(Messages.getString("DOSTMainTab.mapLocation.label")); //$NON-NLS-1$

		ditamapText = new Text(parent, SWT.BORDER);
		ditamapText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		ditamapText.addModifyListener(listener);

		ditamapBrowseButton = new MultiBrowseButton(parent, ditamapText);
		ditamapBrowseButton.setText(Messages.getString("DOSTMainTab.ditamapBrowse.button")); //$NON-NLS-1$
		ditamapBrowseButton.setDescription(Messages.getString("DOSTMainTab.mapSelect")); //$NON-NLS-1$
		ditamapBrowseButton.setExtension("ditamap"); //$NON-NLS-1$
	}

	private void createProfileComponent(Composite parent) {
		Label label = new Label(parent, SWT.NONE);
		label.setText(Messages.getString("DOSTMainTab.profileLocation.label")); //$NON-NLS-1$

		profileText = new Text(parent, SWT.BORDER);
		profileText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		profileText.addModifyListener(listener);

		profileBrowseButton = new MultiBrowseButton(parent, profileText);
		profileBrowseButton.setText(Messages.getString("DOSTMainTab.profileBrowse.button")); //$NON-NLS-1$
		profileBrowseButton.setDescription(Messages.getString("DOSTMainTab.profileSelect")); //$NON-NLS-1$
		profileBrowseButton.setExtension("ditaval"); //$NON-NLS-1$
	}

	private void createTranstypeComponent(Composite parent) {
		Label label = new Label(parent, SWT.NONE);
		label.setText(Messages.getString("DOSTMainTab.transtype.label")); //$NON-NLS-1$

		GridData gridData = new GridData(GridData.FILL_HORIZONTAL);
		gridData.horizontalSpan = 2;

		transtypeText = new Text(parent, SWT.SINGLE | SWT.BORDER);
		transtypeText.setLayoutData(gridData);
		transtypeText.addModifyListener(listener);
	}

	private void createOutputComponent(Composite parent) {
		Label label = new Label(parent, SWT.NONE);
		label.setText(Messages.getString("DOSTMainTab.output.label")); //$NON-NLS-1$

		outputText = new Text(parent, SWT.BORDER);
		outputText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		outputText.addModifyListener(listener);

		outputBrowseButton = new MultiBrowseButton(parent, outputText, true);
		outputBrowseButton.setText(Messages.getString("DOSTMainTab.outputBrowse.button")); //$NON-NLS-1$
		outputBrowseButton.setDescription(Messages.getString("DOSTMainTab.outputSelect")); //$NON-NLS-1$
	}

	private void updateProfile(ILaunchConfiguration configuration) {
		String profile = ""; //$NON-NLS-1$

		try {
			profile = configuration.getAttribute(
					DOSTParameters.DITA_INPUT_VALFILE, ""); //$NON-NLS-1$
		} catch (CoreException ce) {
			Activator.getDefault().log(IStatus.ERROR, ce);
		}

		profileText.setText(profile);
	}

	private void updateOutput(ILaunchConfiguration configuration) {
		String output = ""; //$NON-NLS-1$

		try {
			output = configuration.getAttribute(DOSTParameters.OUTPUT_DIR, ""); //$NON-NLS-1$
		} catch (CoreException ce) {
			Activator.getDefault().log(IStatus.ERROR, ce);
		}

		outputText.setText(output);
	}

	@SuppressWarnings("unchecked")
	private void updateArguments(ILaunchConfiguration configuration) {
		Map args = null;

		try {
			args = configuration.getAttribute(
					DOSTLaunchConfigurationConstants.OTHER_ARGS, (Map) null);
		} catch (CoreException ce) {
			Activator.getDefault().log(IStatus.ERROR, ce);
		}

		argsGroup.setArguments(args);
	}

	/**
	 * Updates the location widgets to match the state of the given launch
	 * configuration.
	 */
	private void updateDitamap(ILaunchConfiguration configuration) {
		String ditamap = ""; //$NON-NLS-1$

		try {
			ditamap = configuration.getAttribute(DOSTParameters.ARGS_INPUT, ""); //$NON-NLS-1$
		} catch (CoreException ce) {
			Activator.getDefault().log(IStatus.ERROR, ce);
		}

		ditamapText.setText(ditamap);
	}

	/**
	 * Updates the argument widgets to match the state of the given launch
	 * configuration.
	 */
	private void updateTranstype(ILaunchConfiguration configuration) {
		String transtype = ""; //$NON-NLS-1$

		try {
			transtype = configuration.getAttribute(DOSTParameters.TRANSTYPE,
					DOSTParameters.DEFAULT_TRANSTYPE);
		} catch (CoreException ce) {
			Activator.getDefault().log(IStatus.ERROR, ce);
		}

		transtypeText.setText(transtype);
	}

	/**
	 * Validates the content of the location field.
	 */
	private boolean validateDitamap(boolean newConfig) {
		String ditamap = ditamapText.getText().trim();

		if (ditamap.length() < 1) {
			if (newConfig) {
				setErrorMessage(null);
				setMessage(Messages.getString("DOSTMainTab.mapLocationSpecify")); //$NON-NLS-1$
			} else {
				setErrorMessage(Messages.getString("DOSTMainTab.mapLocationEmpty")); //$NON-NLS-1$
				setMessage(null);
			}
			return false;
		}

		String expandedLocation = null;
		try {
			expandedLocation = resolveValue(ditamap);

			if (expandedLocation == null) { // a variable that needs to be
				// resolved at runtime
				return true;
			}
		} catch (CoreException e) {
			setErrorMessage(e.getStatus().getMessage());
			return false;
		}

		File file = new File(expandedLocation);

		if (!file.exists()) { // The file does not exist.
			if (!newConfig) {
				setErrorMessage(Messages.getString("DOSTMainTab.mapFileDoesNotExist")); //$NON-NLS-1$
			}
			return false;
		}

		if (!file.isFile()) {
			if (!newConfig) {
				setErrorMessage(Messages.getString("DOSTMainTab.mapFileNotAFile")); //$NON-NLS-1$
			}
			return false;
		}
		return true;
	}

	private void validateVariables(String expression) throws CoreException {
		IStringVariableManager manager = VariablesPlugin.getDefault().getStringVariableManager();
		manager.validateStringVariables(expression);
	}

	private String resolveValue(String expression) throws CoreException {
		String expanded = null;

		try {
			expanded = getValue(expression);
		} catch (CoreException e) { // possibly just a variable that needs to be
			// resolved at runtime
			validateVariables(expression);
			return null;
		}

		return expanded;
	}

	private String getValue(String expression) throws CoreException {
		IStringVariableManager manager = VariablesPlugin.getDefault().getStringVariableManager();
		return manager.performStringSubstitution(expression);
	}

	private boolean validateProfile() {
		String dir = profileText.getText().trim();
		if (dir.length() <= 0) {
			return true;
		}

		String expandedDir = null;
		try {
			expandedDir = resolveValue(dir);
			if (expandedDir == null) { // a variable that needs to be resolved
				// at runtime
				return true;
			}
		} catch (CoreException e) {
			setErrorMessage(e.getStatus().getMessage());
			return false;
		}

		File file = new File(expandedDir);
		if (!file.exists()) { // The directory does not exist.
			setErrorMessage(Messages.getString("DOSTMainTab.profileDoesNotExist")); //$NON-NLS-1$
			return false;
		}
		if (!file.isFile()) {
			setErrorMessage(Messages.getString("DOSTMainTab.profileFileNotAFile")); //$NON-NLS-1$
			return false;
		}
		return true;
	}

	private boolean validateOutput(boolean newConfig) {
		String output = outputText.getText().trim();

		if (output.length() < 1) {
			if (newConfig) {
				setErrorMessage(null);
				setMessage(Messages.getString("DOSTMainTab.outputSpecify")); //$NON-NLS-1$
			} else {
				setErrorMessage(Messages.getString("DOSTMainTab.outputDestinationEmpty")); //$NON-NLS-1$
				setMessage(null);
			}
			return false;
		}

		String expandedLocation = null;
		try {
			expandedLocation = resolveValue(output);

			if (expandedLocation == null) { // a variable that needs to be
				// resolved at runtime
				return true;
			}
		} catch (CoreException e) {
			setErrorMessage(e.getStatus().getMessage());
			return false;
		}

		File file = new File(expandedLocation);

		if (file.exists() && !file.isDirectory()) {
			setErrorMessage(Messages.getString("DOSTMainTab.outputDestinationNotADirectory")); //$NON-NLS-1$
			return false;
		}
		return true;
	}

	private boolean validateTranstype() {
		String transtype = transtypeText.getText().trim();
		return (transtype.length() > 0);
	}

}
