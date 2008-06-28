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
package org.dita_op.editor.internal.ui.wizards;

import java.lang.reflect.InvocationTargetException;
import java.net.URI;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.DITAProjectNature;
import org.eclipse.core.commands.ExecutionException;
import org.eclipse.core.resources.IProject;
import org.eclipse.core.resources.IProjectDescription;
import org.eclipse.core.resources.IWorkspace;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Status;
import org.eclipse.jface.dialogs.ErrorDialog;
import org.eclipse.jface.dialogs.IDialogSettings;
import org.eclipse.jface.operation.IRunnableWithProgress;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.ui.INewWizard;
import org.eclipse.ui.IWorkbench;
import org.eclipse.ui.PlatformUI;
import org.eclipse.ui.dialogs.WizardNewProjectCreationPage;
import org.eclipse.ui.ide.undo.CreateProjectOperation;
import org.eclipse.ui.ide.undo.WorkspaceUndoUtil;
import org.eclipse.ui.wizards.newresource.BasicNewResourceWizard;

public class DITAProjectWizard extends BasicNewResourceWizard implements
		INewWizard {
	private WizardNewProjectCreationPage mainPage;

	/** cache of newly-created project */
	private IProject newProject;

	/**
	 * Creates a wizard for creating a new project resource in the workspace.
	 */
	public DITAProjectWizard() {
		IDialogSettings workbenchSettings = Activator.getDefault().getDialogSettings();
		IDialogSettings section = workbenchSettings.getSection("DITAProjectWizard");//$NON-NLS-1$

		if (section == null) {
			section = workbenchSettings.addNewSection("DITAProjectWizard");//$NON-NLS-1$
		}

		setDialogSettings(section);
	}

	@Override
	public void addPages() {
		super.addPages();

		mainPage = new WizardNewProjectCreationPage("DITAProjectPage");//$NON-NLS-1$
		mainPage.setTitle(Messages.getString("DITAProjectWizard.pageTitle")); //$NON-NLS-1$
		mainPage.setDescription(Messages.getString("DITAProjectWizard.description")); //$NON-NLS-1$

		this.addPage(mainPage);
	}

	/**
	 * Creates a new project resource with the selected name.
	 * <p>
	 * In normal usage, this method is invoked after the user has pressed Finish
	 * on the wizard; the enablement of the Finish button implies that all
	 * controls on the pages currently contain valid values.
	 * </p>
	 * <p>
	 * Note that this wizard caches the new project once it has been
	 * successfully created; subsequent invocations of this method will answer
	 * the same project resource without attempting to create it again.
	 * </p>
	 * 
	 * @return the created project resource, or <code>null</code> if the
	 *         project was not created
	 */
	private IProject createNewProject() {
		if (newProject != null) {
			return newProject;
		}

		// get a project handle
		final IProject newProjectHandle = mainPage.getProjectHandle();

		// get a project descriptor
		URI location = null;
		if (!mainPage.useDefaults()) {
			location = mainPage.getLocationURI();
		}

		IWorkspace workspace = ResourcesPlugin.getWorkspace();
		final IProjectDescription description = workspace.newProjectDescription(newProjectHandle.getName());
		description.setLocationURI(location);

		// create the new project operation
		IRunnableWithProgress op = new IRunnableWithProgress() {
			public void run(IProgressMonitor monitor)
					throws InvocationTargetException {
				CreateProjectOperation op = new CreateProjectOperation(
						description, getWindowTitle());
				try {
					PlatformUI.getWorkbench().getOperationSupport().getOperationHistory().execute(
							op, monitor,
							WorkspaceUndoUtil.getUIInfoAdapter(getShell()));
					DITAProjectNature.toggleNature(newProjectHandle, monitor);
				} catch (ExecutionException e) {
					throw new InvocationTargetException(e);
				} catch (CoreException e) {
					throw new InvocationTargetException(e);
				}
			}
		};

		// run the new project creation operation
		try {
			getContainer().run(true, true, op);
		} catch (InterruptedException e) {
			return null;
		} catch (InvocationTargetException e) {
			Throwable t = e.getTargetException();
			ErrorDialog.openError(
					getShell(),
					Messages.getString("DITAProjectWizard.errorDialogTitle"), //$NON-NLS-1$
					Messages.getString("DITAProjectWizard.errorCreatingProject"), new Status( //$NON-NLS-1$
							IStatus.ERROR, Activator.PLUGIN_ID, -1,
							t.getMessage(), t));
			return null;
		}

		newProject = newProjectHandle;

		return newProject;
	}

	/**
	 * Returns the newly created project.
	 * 
	 * @return the created project, or <code>null</code> if project not
	 *         created
	 */
	public IProject getNewProject() {
		return newProject;
	}

	@Override
	public void init(IWorkbench workbench, IStructuredSelection currentSelection) {
		super.init(workbench, currentSelection);
		setNeedsProgressMonitor(true);
		setWindowTitle(Messages.getString("DITAProjectWizard.windowTitle")); //$NON-NLS-1$
	}

	/**
	 * @see org.eclipse.jface.wizard.Wizard#performFinish()
	 */
	@Override
	public boolean performFinish() {
		createNewProject();

		if (newProject == null) {
			return false;
		}

		selectAndReveal(newProject);
		return true;
	}

}
