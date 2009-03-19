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

import java.io.ByteArrayInputStream;
import java.io.InputStream;
import java.net.URI;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.List;
import java.util.Properties;
import java.util.Set;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.Utils;
import org.dita_op.editor.internal.ui.editors.FormLayoutFactory;
import org.dita_op.editor.internal.ui.templates.DITATemplateContext;
import org.eclipse.core.resources.IFile;
import org.eclipse.core.resources.IWorkspaceRoot;
import org.eclipse.core.resources.ResourcesPlugin;
import org.eclipse.core.runtime.CoreException;
import org.eclipse.core.runtime.IProgressMonitor;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.core.runtime.Path;
import org.eclipse.core.runtime.Status;
import org.eclipse.core.runtime.SubMonitor;
import org.eclipse.core.runtime.SubProgressMonitor;
import org.eclipse.core.runtime.jobs.Job;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.IMenuListener;
import org.eclipse.jface.action.IMenuManager;
import org.eclipse.jface.action.MenuManager;
import org.eclipse.jface.action.Separator;
import org.eclipse.jface.resource.ImageDescriptor;
import org.eclipse.jface.text.BadLocationException;
import org.eclipse.jface.text.templates.TemplateException;
import org.eclipse.jface.viewers.ILabelProvider;
import org.eclipse.jface.viewers.ILabelProviderListener;
import org.eclipse.jface.viewers.IOpenListener;
import org.eclipse.jface.viewers.ISelectionChangedListener;
import org.eclipse.jface.viewers.ISelectionProvider;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.ITreeContentProvider;
import org.eclipse.jface.viewers.OpenEvent;
import org.eclipse.jface.viewers.SelectionChangedEvent;
import org.eclipse.jface.viewers.StructuredSelection;
import org.eclipse.jface.viewers.TreeViewer;
import org.eclipse.jface.viewers.Viewer;
import org.eclipse.swt.SWT;
import org.eclipse.swt.dnd.Clipboard;
import org.eclipse.swt.dnd.DND;
import org.eclipse.swt.dnd.DragSourceEvent;
import org.eclipse.swt.dnd.DragSourceListener;
import org.eclipse.swt.dnd.Transfer;
import org.eclipse.swt.graphics.Image;
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
import org.eclipse.wst.sse.ui.internal.contentoutline.IJFaceNodeAdapter;
import org.eclipse.wst.sse.ui.internal.contentoutline.IJFaceNodeAdapterFactory;
import org.eclipse.wst.xml.core.internal.contentmodel.CMDocument;
import org.eclipse.wst.xml.core.internal.contentmodel.modelquery.CMDocumentManager;
import org.eclipse.wst.xml.core.internal.contentmodel.modelquery.CMDocumentManagerListener;
import org.eclipse.wst.xml.core.internal.contentmodel.modelquery.ModelQuery;
import org.eclipse.wst.xml.core.internal.contentmodel.util.CMDocumentCache;
import org.eclipse.wst.xml.core.internal.modelquery.ModelQueryUtil;
import org.eclipse.wst.xml.core.internal.provisional.document.IDOMNode;
import org.w3c.dom.Document;
import org.w3c.dom.DocumentFragment;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

@SuppressWarnings( { "restriction", "unchecked" })
class MasterSection extends SectionPart {

	private static final Set<String> ACCEPTED_TAGS;
	private static final Set<String> ACCEPT_CHILDREN;

	static {
		ACCEPTED_TAGS = new HashSet<String>();
		ACCEPTED_TAGS.add("topicgroup"); //$NON-NLS-1$
		ACCEPTED_TAGS.add("topichead"); //$NON-NLS-1$
		ACCEPTED_TAGS.add("topicref"); //$NON-NLS-1$
		ACCEPTED_TAGS.add("navref"); //$NON-NLS-1$
		ACCEPTED_TAGS.add("anchor"); //$NON-NLS-1$

		ACCEPT_CHILDREN = new HashSet<String>(4);
		ACCEPT_CHILDREN.add("map");
		ACCEPT_CHILDREN.add("topicref");
		ACCEPT_CHILDREN.add("topichead");
		ACCEPT_CHILDREN.add("topicgroup");
	}

	private MenuManager menuManager = null;
	private MapContentProvider modelProvider = new MapContentProvider();
	private Clipboard clipboard;
	private TreeViewer viewer;
	private URI baseLocation;

	private class NewNodeAction extends Action {
		private final String icon, tag;

		public NewNodeAction(String name, String icon, String tag) {
			super(name);
			this.icon = icon;
			this.tag = tag;
		}

		@Override
		public ImageDescriptor getImageDescriptor() {
			return Activator.getImageDescriptor(icon);
		}

		@Override
		public void run() {
			modelProvider.addNode(tag);
		}
	}

	public MasterSection(Composite parent, FormToolkit toolkit) {
		super(parent, toolkit, Section.TITLE_BAR);
		clipboard = new Clipboard(this.getSection().getDisplay());

		Section section = getSection();
		section.setText(Messages.getString("MasterSection.title")); //$NON-NLS-1$
		section.clientVerticalSpacing = FormLayoutFactory.SECTION_HEADER_VERTICAL_SPACING;
		section.setClient(createClient(section, toolkit));

		menuManager = new MenuManager();
		menuManager.setRemoveAllWhenShown(true);
		menuManager.addMenuListener(new IMenuListener() {

			public void menuAboutToShow(IMenuManager manager) {
				MasterSection.this.menuAboutToShow(manager);
			}
		});

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

	void setBaseLocation(URI baseLocation) {
		this.baseLocation = baseLocation;
	}

	URI getBaseLocation() {
		return baseLocation;
	}

	TreeViewer getViewer() {
		return viewer;
	}

	boolean acceptChildren(Node n) {
		return ACCEPT_CHILDREN.contains(n.getLocalName());
	}

	private void setupDNDSupport() {
		int ops = DND.DROP_MOVE | DND.DROP_COPY;

		viewer.addDropSupport(ops, new Transfer[] {
				ResourceTransfer.getInstance(), NodeTransfer.getInstance() },
				new MasterSectionDropListener(this));

		viewer.addDragSupport(ops,
				new Transfer[] { NodeTransfer.getInstance() },
				new DragSourceListener() {

					public void dragStart(DragSourceEvent event) {
						Node n = getSelection();

						if ("map".equals(n.getLocalName())) {
							event.doit = false;
							return;
						}
					}

					public void dragFinished(DragSourceEvent event) {
						if (event.doit && event.detail == DND.DROP_MOVE) {
							modelProvider.remove(getSelection());
						}
					}

					public void dragSetData(DragSourceEvent event) {
						if (NodeTransfer.getInstance().isSupportedType(
								event.dataType)) {
							Node orig = getSelection();
							event.data = orig.cloneNode(true);
						}
					}
				});
	}

	public Element getSelection() {
		IStructuredSelection selection = (IStructuredSelection) viewer.getSelection();
		return (Element) selection.getFirstElement();
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
				MasterSection.this.open(getSelection());
			}
		});
	}

	private void menuAboutToShow(IMenuManager manager) {
		final Element selection = getSelection();

		if (selection != null) {
			MenuManager subNew = new MenuManager("New");
			subNew.add(new NewNodeAction("&Topic", ImageConstants.ICON_TOPIC,
					"topicref"));
			subNew.add(new NewNodeAction("&Heading", ImageConstants.ICON_HEAD,
					"topichead"));
			subNew.add(new NewNodeAction("&Group", ImageConstants.ICON_GROUP,
					"topicgroup"));
			subNew.add(new NewNodeAction("&Navref", ImageConstants.ICON_NAVREF,
					"navref"));
			subNew.add(new NewNodeAction("&Anchor", ImageConstants.ICON_ANCHOR,
					"anchor"));
			manager.add(subNew);

			if (getReference(selection) != null) {
				manager.add(new Action("&Open") {

					@Override
					public void run() {
						MasterSection.this.open(getSelection());
					}

				});
			}

			manager.add(new Separator());
			boolean ismap = "map".equals(selection.getLocalName());

			Action cutAction = new Action("C&ut",
					Activator.getImageDescriptor(ImageConstants.ICON_CUT)) {

				@Override
				public void run() {
					Node copy = selection.cloneNode(true);

					clipboard.setContents(new Object[] { copy },
							new Transfer[] { NodeTransfer.getInstance() });

					modelProvider.remove(selection);
				}

			};
			cutAction.setEnabled(!ismap);
			manager.add(cutAction);

			Action copyAction = new Action("&Copy",
					Activator.getImageDescriptor(ImageConstants.ICON_COPY)) {

				@Override
				public void run() {
					Node copy = selection.cloneNode(true);

					clipboard.setContents(new Object[] { copy },
							new Transfer[] { NodeTransfer.getInstance() });
				}

			};
			copyAction.setEnabled(!ismap);
			manager.add(copyAction);

			Action pasteAction = new Action("&Paste",
					Activator.getImageDescriptor(ImageConstants.ICON_PASTE)) {

				@Override
				public void run() {
					DocumentFragment fragment = (DocumentFragment) clipboard.getContents(NodeTransfer.getInstance());
					if (fragment != null) {
						modelProvider.addNode(selection, fragment);
					}
				}

			};
			pasteAction.setEnabled(clipboard.getContents(NodeTransfer.getInstance()) != null);
			manager.add(pasteAction);

			Action deleteAction = new Action("&Delete",
					Activator.getImageDescriptor(ImageConstants.ICON_REMOVE)) {

				@Override
				public void run() {
					modelProvider.remove(selection);
				}

			};
			deleteAction.setEnabled(!ismap);
			manager.add(deleteAction);

			if (baseLocation != null) {
				final int stubsCount = countStubs(selection);
				Action generateAction = new Action("&Generate stubs") {

					@Override
					public void run() {
						Job job = new Job("Generating stubs") {

							@Override
							protected IStatus run(IProgressMonitor monitor) {
								SubMonitor progress = SubMonitor.convert(
										monitor, stubsCount);

								try {
									generateStubs(selection,
											progress.newChild(stubsCount));
									return Status.OK_STATUS;
								} catch (CoreException e) {
									return e.getStatus();
								} finally {
									progress.done();
								}
							}

						};

						job.setPriority(Job.INTERACTIVE);
						job.schedule();
					}
				};

				manager.add(new Separator());
				generateAction.setEnabled(stubsCount > 0);
				manager.add(generateAction);
			}
		}
	}

	private int countStubs(Node n) {
		int count = 0;
		String localName = n.getLocalName();

		if ("topicref".equals(localName) || "navref".equals(localName)) {
			String ref = getReference(n);

			if (ref != null) {
				IFile target = getTargetFile(ref);

				if (target == null || !target.exists()) {
					count++;
				}
			} else {
				count++;
			}
		}

		NodeList children = n.getChildNodes();
		for (int i = 0; i < children.getLength(); i++) {
			count += countStubs(children.item(i));
		}

		return count;
	}

	private void generateStubs(Element n, IProgressMonitor monitor)
			throws CoreException {
		String localName = n.getLocalName();

		if ("topicref".equals(localName)) {
			generateTopicStub(n, monitor);
		} else if ("navref".equals(localName)) {
			generateMapStub(n, monitor);
		}

		NodeList children = n.getChildNodes();
		for (int i = 0; i < children.getLength(); i++) {
			Node child = children.item(i);

			if (child instanceof Element) {
				generateStubs((Element) child, monitor);
			}
		}
	}

	private void generateTopicStub(Element n, IProgressMonitor monitor)
			throws CoreException {
		String ref = n.getAttribute("href");

		if (ref == null) {
			ref = computeTopicFilename(n);
			n.setAttribute("href", ref);
		}

		String navtitle = n.getAttribute("navtitle");
		IFile target = getTargetFile(ref);

		if (!target.exists()) {
			String templateId = "org.dita_op.editor.template.topic";
			String type = n.getAttribute("type");

			if ("concept".equals(type)) {
				templateId = "org.dita_op.editor.template.concept";
			} else if ("task".equals(type)) {
				templateId = "org.dita_op.editor.template.task";
			} else if ("reference".equals(type)) {
				templateId = "org.dita_op.editor.template.reference";
			}

			target.create(openContentStream(templateId, navtitle), true,
					new SubProgressMonitor(monitor, 1));
		}
	}

	private void generateMapStub(Element n, IProgressMonitor monitor)
			throws CoreException {
		String ref = n.getAttribute("mapref");

		if (ref == null) {
			ref = computeMapFilename(n);
			n.setAttribute("mapref", ref);
		}

		IFile target = getTargetFile(ref);

		if (!target.exists()) {
			target.create(openContentStream("org.dita_op.editor.template.map",
					null), true, new SubProgressMonitor(monitor, 1));
		}
	}

	private String computeTopicFilename(Element n) {
		String prefix = null;
		String navtitle = n.getAttribute("navtitle");
		String id = n.getAttribute("id");
		String type = n.getAttribute("type");

		if (navtitle != null) {
			prefix = Utils.slugify(navtitle);
		} else if (id != null) {
			prefix = Utils.slugify(id);
		} else if (type != null) {
			prefix = Utils.slugify(type);
		} else {
			prefix = "topic";
		}

		String filename = prefix.concat(".dita");
		IFile target = getTargetFile(filename);

		for (int i = 1; target.exists(); i++) {
			filename = prefix.concat("_").concat(Integer.toString(i)).concat(
					".dita");
			target = getTargetFile(filename);
		}

		return filename;
	}

	private String computeMapFilename(Element n) {
		String prefix = null;

		String id = n.getAttribute("id");
		if (id != null) {
			prefix = Utils.slugify(id);
		} else {
			prefix = "map";
		}

		String filename = prefix.concat(".ditamap");
		IFile target = getTargetFile(filename);

		for (int i = 1; target.exists(); i++) {
			filename = prefix.concat("_").concat(Integer.toString(i)).concat(
					".ditamap");
			target = getTargetFile(filename);
		}

		return filename;
	}

	private InputStream openContentStream(String templateId, String title)
			throws CoreException {
		Properties vars = null;

		if (title != null) {
			vars = new Properties();
			vars.setProperty("title", title);
		}

		try {
			String contents = DITATemplateContext.evaluateTemplate(templateId,
					vars);
			return new ByteArrayInputStream(contents.getBytes());
		} catch (BadLocationException e) {
			throw newCoreException(e);
		} catch (TemplateException e) {
			throw newCoreException(e);
		}
	}

	private CoreException newCoreException(Exception e) throws CoreException {
		IStatus status = Activator.getDefault().newStatus(IStatus.ERROR, e);
		return new CoreException(status);
	}

	private void open(Node n) {
		String ref = getReference(n);

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

	private String getReference(Node n) {
		if (n instanceof Element) {
			Element elmnt = (Element) n;
			String href = elmnt.getAttribute("href"); //$NON-NLS-1$

			if (href == null) {
				href = elmnt.getAttribute("mapref"); //$NON-NLS-1$
			}

			return href;
		} else {
			return null;
		}
	}

	private IFile getTargetFile(String ref) {
		URI targetURI = URI.create(ref);

		if (baseLocation != null && !targetURI.isAbsolute()) {
			targetURI = baseLocation.resolve(targetURI);

			IWorkspaceRoot root = ResourcesPlugin.getWorkspace().getRoot();
			return root.getFile(new Path(targetURI.toString()));
		}

		return null;
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

	private class MapContentProvider implements ITreeContentProvider,
			ILabelProvider, CMDocumentManagerListener {

		private TreeViewer viewer;
		private Document document;

		private void doDelayedRefreshForViewers() {
			if ((viewer != null) && !viewer.getControl().isDisposed()) {
				viewer.getControl().getDisplay().asyncExec(new Runnable() {
					public void run() {
						if ((viewer != null)
								&& !viewer.getControl().isDisposed()) {
							viewer.refresh(true);
						}
					}
				});
			}
		}

		public void addNode(Node parent, Node n) {
			if (parent == null) {
				parent = document.getDocumentElement();
			}

			n = document.importNode(n, true);
			parent.appendChild(n);

			viewer.add(parent, n);
			viewer.setSelection(new StructuredSelection(n), true);
			markDirty();
		}

		public void addNode(String name) {
			Node parent = getSelection();

			if (parent == null) {
				parent = document.getDocumentElement();
			}

			Element child = document.createElement(name);
			parent.appendChild(child);

			viewer.add(parent, child);
			viewer.setSelection(new StructuredSelection(child), true);
			markDirty();
		}

		public void remove(Node n) {
			n.getParentNode().removeChild(n);
			viewer.remove(n);
			markDirty();
		}

		/**
		 * @see org.eclipse.jface.viewers.ArrayContentProvider#inputChanged(org.eclipse.jface.viewers.Viewer,
		 *      java.lang.Object, java.lang.Object)
		 */
		public void inputChanged(Viewer viewer, Object oldInput, Object newInput) {
			unlisten();
			this.viewer = (TreeViewer) viewer;
			this.document = (Document) newInput;
			listen();
		}

		private void unlisten() {
			if (document != null) {
				ModelQuery mq = ModelQueryUtil.getModelQuery(document);

				if (mq != null) {
					CMDocumentManager dm = mq.getCMDocumentManager();
					if (dm != null) {
						dm.removeListener(this);
					}
				}
			}

			if (document instanceof IDOMNode) {
				IJFaceNodeAdapterFactory factory = (IJFaceNodeAdapterFactory) ((IDOMNode) document).getModel().getFactoryRegistry().getFactoryFor(
						IJFaceNodeAdapter.class);

				if (factory != null) {
					factory.removeListener(viewer);
				}
			}
		}

		private void listen() {
			if (document instanceof IDOMNode) {
				IJFaceNodeAdapterFactory factory = (IJFaceNodeAdapterFactory) ((IDOMNode) document).getModel().getFactoryRegistry().getFactoryFor(
						IJFaceNodeAdapter.class);

				if (factory != null) {
					factory.addListener(viewer);
				}
			}

			if (document != null) {
				ModelQuery mq = ModelQueryUtil.getModelQuery(document);

				if (mq != null) {
					CMDocumentManager dm = mq.getCMDocumentManager();
					if (dm != null) {
						dm.setPropertyEnabled(
								CMDocumentManager.PROPERTY_ASYNC_LOAD, true);
						dm.addListener(this);
					}
				}
			}
		}

		public void dispose() {
			unlisten();
		}

		public Image getImage(Object element) {
			Element n = (Element) element;
			String tagName = n.getTagName();

			if ("map".equals(tagName)) { //$NON-NLS-1$
				return Activator.getDefault().getImage(
						ImageConstants.ICON_DITAMAP);
			} else if ("topicref".equals(tagName)) { //$NON-NLS-1$
				return Activator.getDefault().getImage(
						ImageConstants.ICON_TOPIC);
			} else if ("topicgroup".equals(tagName)) { //$NON-NLS-1$
				return Activator.getDefault().getImage(
						ImageConstants.ICON_GROUP);
			} else if ("topichead".equals(tagName)) { //$NON-NLS-1$
				return Activator.getDefault().getImage(ImageConstants.ICON_HEAD);
			} else if ("anchor".equals(tagName)) { //$NON-NLS-1$
				return Activator.getDefault().getImage(
						ImageConstants.ICON_ANCHOR);
			} else if ("navref".equals(tagName)) { //$NON-NLS-1$
				return Activator.getDefault().getImage(
						ImageConstants.ICON_NAVREF);
			} else {
				return null;
			}
		}

		public String getText(Object element) {
			Element n = (Element) element;
			String tagName = n.getTagName();

			if ("topicgroup".equals(tagName)) { //$NON-NLS-1$
				String id = n.getAttribute("id"); //$NON-NLS-1$

				if (id != null) {
					return id;
				} else {
					return Messages.getString("MasterSection.group.label"); //$NON-NLS-1$
				}
			} else if ("anchor".equals(tagName)) { //$NON-NLS-1$
				String id = n.getAttribute("id"); //$NON-NLS-1$

				if (id != null) {
					return id;
				} else {
					return Messages.getString("MasterSection.anchor.label"); //$NON-NLS-1$
				}
			} else if ("navref".equals(tagName)) { //$NON-NLS-1$
				String mapref = n.getAttribute("mapref"); //$NON-NLS-1$

				if (mapref != null) {
					return mapref;
				} else {
					return Messages.getString("MasterSection.navref.label"); //$NON-NLS-1$
				}
			} else {
				StringBuilder buffer = new StringBuilder();
				String href = n.getAttribute("href"); //$NON-NLS-1$
				String title = n.getAttribute("navtitle"); //$NON-NLS-1$

				if (title == null) {
					title = n.getAttribute("title"); //$NON-NLS-1$
				}

				if (title != null) {
					buffer.append(title);
				} else {
					buffer.append(n.getLocalName());
				}

				if (href != null) {
					buffer.append(" - ").append(href); //$NON-NLS-1$
				}

				return buffer.toString();
			}
		}

		public void propertyChanged(CMDocumentManager cmDocumentManager,
				String propertyName) {
			if (cmDocumentManager.getPropertyEnabled(CMDocumentManager.PROPERTY_AUTO_LOAD)) {
				doDelayedRefreshForViewers();
			}
		}

		public void cacheCleared(CMDocumentCache cache) {
			doDelayedRefreshForViewers();
		}

		public void cacheUpdated(CMDocumentCache cache, String uri,
				int oldStatus, int newStatus, CMDocument cmDocument) {
			if ((newStatus == CMDocumentCache.STATUS_LOADED)
					|| (newStatus == CMDocumentCache.STATUS_ERROR)) {
				doDelayedRefreshForViewers();
			}
		}

		public Object[] getChildren(Object parentElement) {
			Node n = (Node) parentElement;
			NodeList nodes = n.getChildNodes();
			List<Node> children = new ArrayList<Node>();

			for (int i = 0; i < nodes.getLength(); i++) {
				Node child = nodes.item(i);
				if (ACCEPTED_TAGS.contains(child.getLocalName())) {
					children.add(child);
				}
			}

			return children.toArray();
		}

		public Object getParent(Object element) {
			Node n = (Node) element;
			return n.getParentNode();
		}

		public boolean hasChildren(Object element) {
			return getChildren(element).length > 0;
		}

		public Object[] getElements(Object inputElement) {
			Document document = (Document) inputElement;
			return new Object[] { document.getDocumentElement() };
		}

		public void addListener(ILabelProviderListener listener) {
			// noop
		}

		public boolean isLabelProperty(Object element, String property) {
			return false;
		}

		public void removeListener(ILabelProviderListener listener) {
			// noop
		}

	}

}
