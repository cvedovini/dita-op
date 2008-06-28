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
package org.dita_op.editor.internal.ui.editors;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.lang.reflect.InvocationTargetException;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.editors.model.ProfileModel;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IResourceChangeEvent;
import org.eclipse.core.resources.IResourceChangeListener;
import org.eclipse.core.resources.IStorage;
import org.eclipse.core.resources.IWorkspace;
import org.eclipse.core.resources.IWorkspaceRunnable;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IPath;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Platform;
import org.eclipse.core.runtime.SubMonitor;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.IToolBarManager;
import org.eclipse.jface.dialogs.Dialog;
import org.eclipse.jface.dialogs.ErrorDialog;
import org.eclipse.jface.operation.IRunnableWithProgress;
import org.eclipse.swt.custom.BusyIndicator;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Display;
import org.eclipse.ui.IEditorInput;
import org.eclipse.ui.IEditorPart;
import org.eclipse.ui.IEditorSite;
import org.eclipse.ui.IFileEditorInput;
import org.eclipse.ui.IStorageEditorInput;
import org.eclipse.ui.IWorkbenchPage;
import org.eclipse.ui.PartInitException;
import org.eclipse.ui.PlatformUI;
import org.eclipse.ui.dialogs.SaveAsDialog;
import org.eclipse.ui.forms.ManagedForm;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.ScrolledForm;
import org.eclipse.ui.part.EditorPart;
import org.eclipse.ui.part.FileEditorInput;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class ProfileEditor extends EditorPart implements
		IResourceChangeListener {

	private ManagedForm mform;
	private ProfileModel model;
	private IFile location;
	boolean readonly = true;

	public ProfileEditor() {
		super();
		ResourcesPlugin.getWorkspace().addResourceChangeListener(this);
	}

	@Override
	public void init(IEditorSite site, IEditorInput editorInput)
			throws PartInitException {
		if (!(editorInput instanceof IStorageEditorInput)) {
			throw new PartInitException(
					Messages.getString("ProcessingProfileEditor.invalidInput")); //$NON-NLS-1$
		}

		if (editorInput instanceof IFileEditorInput) {
			location = ((IFileEditorInput) editorInput).getFile();
			readonly = false;
		}

		setSite(site);
		setInput(editorInput);
		setPartName(editorInput.getName());
		setTitleToolTip(editorInput.getToolTipText());

		try {
			IStorage storage = ((IStorageEditorInput) editorInput).getStorage();
			model = ProfileModel.loadModel(storage.getContents());
		} catch (CoreException e) {
			throw new PartInitException(e.getStatus());
		} catch (IOException e) {
			throw (PartInitException) new PartInitException(e.getMessage()).initCause(e);
		}
	}

	/**
	 * @see org.eclipse.ui.forms.editor.FormEditor#dispose()
	 */
	@Override
	public void dispose() {
		ResourcesPlugin.getWorkspace().removeResourceChangeListener(this);
		super.dispose();
	}

	/**
	 * Closes all project files on project close.
	 */
	public void resourceChanged(final IResourceChangeEvent event) {
		if (event.getType() == IResourceChangeEvent.PRE_CLOSE) {
			Display.getDefault().asyncExec(new Runnable() {
				public void run() {
					IWorkbenchPage[] pages = getSite().getWorkbenchWindow().getPages();

					for (int i = 0; i < pages.length; i++) {
						if (((FileEditorInput) getEditorInput()).getFile().getProject().equals(
								event.getResource())) {
							IEditorPart editorPart = pages[i].findEditor(getEditorInput());
							pages[i].closeEditor(editorPart, true);
						}
					}
				}
			});
		}
	}

	@Override
	public void createPartControl(Composite parent) {
		FormToolkit toolkit = createToolkit(parent.getDisplay());
		final ScrolledForm form = toolkit.createScrolledForm(parent);
		mform = new ManagedForm(toolkit, form) {

			/**
			 * @see org.eclipse.ui.forms.ManagedForm#dirtyStateChanged()
			 */
			@Override
			public void dirtyStateChanged() {
				super.dirtyStateChanged();
				firePropertyChange(PROP_DIRTY);
			}

		};

		toolkit.decorateFormHeading(form.getForm());
		form.setText(getTitle());
		form.setImage(Activator.getDefault().getImage(
				ImageConstants.ICON_PROFILE));

		// Display the help icon only if the langref plugin is present
		if (Platform.getBundle("org.dita_op.dita.langref") != null) { //$NON-NLS-1$
			IToolBarManager manager = form.getToolBarManager();
			Action helpAction = new Action("help") { //$NON-NLS-1$
				public void run() {
					BusyIndicator.showWhile(form.getForm().getDisplay(),
							new Runnable() {
								public void run() {
									PlatformUI.getWorkbench().getHelpSystem().displayHelpResource(
											"/org.dita_op.dita.langref/common/about-ditaval.html"); //$NON-NLS-1$
								}
							});
				}
			};
			helpAction.setToolTipText(Messages.getString("ProfileEditor.help.tooltip")); //$NON-NLS-1$
			helpAction.setImageDescriptor(Activator.getImageDescriptor(ImageConstants.ICON_HELP));
			manager.add(helpAction);
		}

		Composite body = form.getBody();
		body.setLayout(FormLayoutFactory.createFormGridLayout(false, 1));
		body.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));

		mform.addPart(new StyleConflictSection(body, toolkit));
		new PropsMasterDetailsBlock().createContent(mform);

		mform.initialize();
		mform.setInput(model);
	}

	@Override
	public boolean isDirty() {
		return !readonly && mform.isDirty();
	}

	@Override
	public void setFocus() {
		mform.setFocus();
	}

	protected FormToolkit createToolkit(Display display) {
		// Create a toolkit that shares colors between editors.
		return new FormToolkit(Activator.getDefault().getFormColors(display));
	}

	/**
	 * Saves the multi-page editor's document.
	 */
	@Override
	public void doSave(IProgressMonitor monitor) {
		SubMonitor progress = SubMonitor.convert(monitor, 3);

		try {
			mform.commit(true);
			progress.worked(1);

			ByteArrayOutputStream out = new ByteArrayOutputStream();
			ProfileModel.saveModel(model, out);

			final ByteArrayInputStream in = new ByteArrayInputStream(
					out.toByteArray());

			IWorkspaceRunnable action = new IWorkspaceRunnable() {
				public void run(IProgressMonitor monitor) throws CoreException {
					if (location.exists()) {
						location.setContents(in, false, true, monitor);
					} else {
						location.create(in, false, monitor);
					}
				}
			};

			ResourcesPlugin.getWorkspace().run(action, location,
					IWorkspace.AVOID_UPDATE, progress.newChild(1));

			firePropertyChange(PROP_DIRTY);
		} catch (CoreException e) {
			reportError(e);
		} catch (IOException e) {
			reportError(e);
		} finally {
			progress.done();
		}

	}

	private void reportError(Exception e) {
		IStatus status = Activator.getDefault().newStatus(IStatus.ERROR, e);
		Activator.getDefault().getLog().log(status);
		ErrorDialog.openError(getSite().getShell(),
				Messages.getString("ProfileEditor.errorDialog.title"), //$NON-NLS-1$
				e.getLocalizedMessage(), status);
	}

	@Override
	public void doSaveAs() {
		SaveAsDialog dialog = new SaveAsDialog(getSite().getShell());

		if (location != null) {
			dialog.setOriginalFile(location);
		}

		if (dialog.open() == Dialog.OK) {
			IPath path = dialog.getResult();
			location = ResourcesPlugin.getWorkspace().getRoot().getFile(path);

			IRunnableWithProgress op = new IRunnableWithProgress() {
				public void run(IProgressMonitor monitor) {
					doSave(monitor);
				}
			};

			try {
				getSite().getWorkbenchWindow().run(false, false, op);
			} catch (InvocationTargetException e) {
				Activator.getDefault().log(IStatus.ERROR, e);
			} catch (InterruptedException e) {
				Activator.getDefault().log(IStatus.ERROR, e);
			}
		}
	}

	/**
	 * @see org.eclipse.ui.part.EditorPart#isSaveAsAllowed()
	 */
	@Override
	public boolean isSaveAsAllowed() {
		return true;
	}

}
