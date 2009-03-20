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
package org.dita_op.editor.internal.ui.editors.map;

import java.net.URI;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.dita_op.editor.internal.ui.editors.map.model.Descriptor;
import org.dita_op.editor.internal.ui.editors.map.model.MapContentProvider;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IWorkspaceRoot;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Path;
import org.eclipse.core.runtime.jobs.Job;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.GroupMarker;
import org.eclipse.jface.action.IMenuListener;
import org.eclipse.jface.action.IMenuManager;
import org.eclipse.jface.action.MenuManager;
import org.eclipse.jface.action.Separator;
import org.eclipse.jface.viewers.IOpenListener;
import org.eclipse.jface.viewers.ISelectionChangedListener;
import org.eclipse.jface.viewers.ISelectionProvider;
import org.eclipse.jface.viewers.OpenEvent;
import org.eclipse.jface.viewers.SelectionChangedEvent;
import org.eclipse.jface.viewers.TreeViewer;
import org.eclipse.swt.SWT;
import org.eclipse.swt.dnd.Clipboard;
import org.eclipse.swt.dnd.DND;
import org.eclipse.swt.dnd.Transfer;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Menu;
import org.eclipse.swt.widgets.Tree;
import org.eclipse.ui.IWorkbenchPage;
import org.eclipse.ui.PartInitException;
import org.eclipse.ui.PlatformUI;
import org.eclipse.ui.forms.SectionPart;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;
import org.eclipse.ui.ide.IDE;
import org.eclipse.ui.part.ResourceTransfer;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

@SuppressWarnings( { "unchecked" })//$NON-NLS-1$
public class MasterSection extends SectionPart implements IMenuListener {

	private URI baseLocation;
	private Clipboard clipboard;
	private MenuManager menuManager = null;
	private MapContentProvider modelProvider = new MapContentProvider();
	private TreeViewer viewer;

	public MasterSection(Composite parent, FormToolkit toolkit) {
		super(parent, toolkit, Section.TITLE_BAR);
		clipboard = new Clipboard(this.getSection().getDisplay());

		Section section = getSection();
		section.setText(Messages.getString("MasterSection.title")); //$NON-NLS-1$
		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;
		section.setClient(createClient(section, toolkit));

		menuManager = new MenuManager();
		menuManager.setRemoveAllWhenShown(true);
		menuManager.addMenuListener(this);

		Menu popup = menuManager.createContextMenu(viewer.getControl());
		viewer.getControl().setMenu(popup);

		setupDNDSupport();
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#dispose()
	 */
	@Override
	public void dispose() {
		clipboard.dispose();
		super.dispose();
	}

	public ISelectionProvider getSelectionProvider() {
		return viewer;
	}

	public void menuAboutToShow(IMenuManager manager) {
		final Element elt = modelProvider.getSelection();
		Descriptor desc = Descriptor.getDescriptor(elt);

		MenuManager newChild = new MenuManager(
				Messages.getString("MasterSection.menu.new_child"), "new_child"); //$NON-NLS-1$ //$NON-NLS-2$
		newChild.add(new GroupMarker("addition")); //$NON-NLS-1$
		manager.add(newChild);

		MenuManager newSibing = new MenuManager(
				Messages.getString("MasterSection.menu.new_sibling"), "new_sibling"); //$NON-NLS-1$ //$NON-NLS-2$
		newSibing.add(new GroupMarker("addition")); //$NON-NLS-1$
		manager.add(newSibing);

		if (getReference(elt) != null) {
			manager.add(new Action(
					Messages.getString("MasterSection.menu.open")) { //$NON-NLS-1$

				@Override
				public void run() {
					MasterSection.this.open(elt);
				}

			});
		}

		manager.add(new Separator("edit")); //$NON-NLS-1$
		boolean ismap = Descriptor.MAP.instanceOf(elt);

		if (!ismap) {
			manager.add(new Action(
					Messages.getString("MasterSection.menu.cut"), //$NON-NLS-1$
					Activator.getImageDescriptor(ImageConstants.ICON_CUT)) {

				@Override
				public void run() {
					Node copy = elt.cloneNode(true);

					clipboard.setContents(new Object[] { copy },
							new Transfer[] { NodeTransfer.getInstance() });

					modelProvider.remove(elt);
				}

			});

			manager.add(new Action(
					Messages.getString("MasterSection.menu.copy"), //$NON-NLS-1$
					Activator.getImageDescriptor(ImageConstants.ICON_COPY)) {

				@Override
				public void run() {
					Node copy = elt.cloneNode(true);

					clipboard.setContents(new Object[] { copy },
							new Transfer[] { NodeTransfer.getInstance() });
				}

			});
		}

		Action pasteAction = new Action(
				Messages.getString("MasterSection.menu.paste"), //$NON-NLS-1$
				Activator.getImageDescriptor(ImageConstants.ICON_PASTE)) {

			@Override
			public void run() {
				Node n = (Node) clipboard.getContents(NodeTransfer.getInstance());

				if (n != null) {
					modelProvider.addChildNode(elt, n);
				}
			}

		};

		Node pasted = (Node) clipboard.getContents(NodeTransfer.getInstance());
		pasteAction.setEnabled(pasted != null && desc.accept(pasted));
		manager.add(pasteAction);

		if (!ismap) {
			manager.add(new Separator());
			manager.add(new Action(
					Messages.getString("MasterSection.menu.delete"), //$NON-NLS-1$
					Activator.getImageDescriptor(ImageConstants.ICON_REMOVE)) {

				@Override
				public void run() {
					modelProvider.remove(elt);
				}

			});
		}

		manager.add(new Separator("additions")); //$NON-NLS-1$

		if (baseLocation != null) {
			final int stubsCount = countStubs(elt);
			Action generateAction = new Action(
					Messages.getString("MasterSection.menu.generateStubs")) { //$NON-NLS-1$

				@Override
				public void run() {
					Job job = new GenerateStubsJob(MasterSection.this,
							stubsCount, elt);
					job.schedule();
				}
			};

			generateAction.setEnabled(stubsCount > 0);
			manager.add(generateAction);
		}

		desc.contributeMenuItems(manager, modelProvider);
	}

	/**
	 * @see org.eclipse.ui.forms.AbstractFormPart#setFormInput(java.lang.Object)
	 */
	@Override
	public boolean setFormInput(Object input) {
		if (input instanceof Document) {
			viewer.setInput(input);
			viewer.expandToLevel(2);
			return true;
		} else {
			viewer.setInput(null);
			return false;
		}
	}

	Document getDocument() {
		return modelProvider.getDocument();
	}

	IFile getTargetFile(String ref) {
		URI targetURI = URI.create(ref);

		if (baseLocation != null && !targetURI.isAbsolute()) {
			targetURI = baseLocation.resolve(targetURI);

			IWorkspaceRoot root = ResourcesPlugin.getWorkspace().getRoot();
			return root.getFile(new Path(targetURI.toString()));
		}

		return null;
	}

	public URI getBaseLocation() {
		return baseLocation;
	}

	void setBaseLocation(URI baseLocation) {
		this.baseLocation = baseLocation;
	}

	private int countStubs(Element elt) {
		int count = 0;

		if (Descriptor.TOPICREF.instanceOf(elt)
				|| Descriptor.NAVREF.instanceOf(elt)) {
			String ref = getReference(elt);

			if (ref != null) {
				IFile target = getTargetFile(ref);

				if (target == null || !target.exists()) {
					count++;
				}
			} else {
				count++;
			}
		}

		NodeList children = elt.getChildNodes();
		for (int i = 0; i < children.getLength(); i++) {
			Node n = children.item(i);

			if (n instanceof Element) {
				count += countStubs((Element) n);
			}
		}

		return count;
	}

	private Composite createClient(Composite parent, FormToolkit toolkit) {
		Composite container = toolkit.createComposite(parent);
		container.setLayout(new GridLayout(2, false));
		container.setLayoutData(new GridData(GridData.FILL_BOTH));

		createViewer(container, toolkit);

		return container;
	}

	private void createViewer(Composite parent, FormToolkit toolkit) {
		Tree tree = toolkit.createTree(parent, SWT.SINGLE);
		tree.setLayoutData(new GridData(GridData.FILL_BOTH));

		viewer = new TreeViewer(tree);
		viewer.setContentProvider(modelProvider);
		viewer.setLabelProvider(modelProvider);

		viewer.addSelectionChangedListener(new ISelectionChangedListener() {

			public void selectionChanged(SelectionChangedEvent event) {
				getManagedForm().fireSelectionChanged(MasterSection.this,
						event.getSelection());
			}

		});

		viewer.addOpenListener(new IOpenListener() {

			public void open(OpenEvent event) {
				MasterSection.this.open(modelProvider.getSelection());
			}
		});
	}

	private String getReference(Element elt) {
		String href = elt.getAttribute("href"); //$NON-NLS-1$

		if (href == null) {
			href = elt.getAttribute("mapref"); //$NON-NLS-1$
		}

		return href;
	}

	private void setupDNDSupport() {
		int ops = DND.DROP_MOVE | DND.DROP_COPY;

		viewer.addDropSupport(ops, new Transfer[] {
				ResourceTransfer.getInstance(), NodeTransfer.getInstance() },
				new MSDropListener(viewer, this));

		viewer.addDragSupport(ops,
				new Transfer[] { NodeTransfer.getInstance() },
				new MSDragSourceListener(modelProvider));
	}

	private void open(Element elt) {
		String ref = getReference(elt);

		if (ref != null) {
			IFile target = getTargetFile(ref);

			if (target != null && target.exists()) {
				try {
					IWorkbenchPage page = PlatformUI.getWorkbench().getActiveWorkbenchWindow().getActivePage();
					IDE.openEditor(page, target, true);
				} catch (PartInitException e) {
					Activator.getDefault().log(IStatus.WARNING, e);
				}
			}
		}
	}

}
