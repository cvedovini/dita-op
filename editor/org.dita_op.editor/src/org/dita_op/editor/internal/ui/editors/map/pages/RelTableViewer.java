package org.dita_op.editor.internal.ui.editors.map.pages;

import java.net.URI;
import java.util.ArrayList;
import java.util.IdentityHashMap;
import java.util.List;
import java.util.Map;

import org.dita_op.editor.internal.Activator;
import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.editors.map.NodeTransfer;
import org.dita_op.editor.internal.ui.editors.map.model.Descriptor;
import org.dita_op.editor.internal.utils.DOMUtils;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.runtime.Assert;
import org.eclipse.core.runtime.ListenerList;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.GroupMarker;
import org.eclipse.jface.action.IMenuListener;
import org.eclipse.jface.action.IMenuManager;
import org.eclipse.jface.action.MenuManager;
import org.eclipse.jface.action.Separator;
import org.eclipse.jface.util.SafeRunnable;
import org.eclipse.jface.viewers.ContentViewer;
import org.eclipse.jface.viewers.IBaseLabelProvider;
import org.eclipse.jface.viewers.IContentProvider;
import org.eclipse.jface.viewers.ILabelProvider;
import org.eclipse.jface.viewers.IOpenListener;
import org.eclipse.jface.viewers.ISelection;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.OpenEvent;
import org.eclipse.jface.viewers.StructuredSelection;
import org.eclipse.jface.viewers.ViewerDropAdapter;
import org.eclipse.swt.SWT;
import org.eclipse.swt.custom.CLabel;
import org.eclipse.swt.dnd.Clipboard;
import org.eclipse.swt.dnd.DND;
import org.eclipse.swt.dnd.DragSource;
import org.eclipse.swt.dnd.DragSourceEvent;
import org.eclipse.swt.dnd.DragSourceListener;
import org.eclipse.swt.dnd.DropTarget;
import org.eclipse.swt.dnd.DropTargetEvent;
import org.eclipse.swt.dnd.Transfer;
import org.eclipse.swt.dnd.TransferData;
import org.eclipse.swt.events.MouseEvent;
import org.eclipse.swt.events.MouseListener;
import org.eclipse.swt.graphics.Color;
import org.eclipse.swt.graphics.Point;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Control;
import org.eclipse.swt.widgets.Display;
import org.eclipse.swt.widgets.Widget;
import org.eclipse.ui.part.ResourceTransfer;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

class RelTableViewer extends ContentViewer implements IMenuListener {

	protected static final int KEYS = SWT.CONTROL | SWT.COMMAND | SWT.SHIFT;

	private final Composite container;
	private final URI baseLocation;
	private final Clipboard clipboard;
	private final Map<Object, LabelEx> topicMap = new IdentityHashMap<Object, LabelEx>();
	private final Map<Object, Composite> cellMap = new IdentityHashMap<Object, Composite>();
	private final Map<Object, CLabel> colspecMap = new IdentityHashMap<Object, CLabel>();
	private final MenuManager menuManager;
	private final DropListener dropListener;
	private final DragListener dragListener;

	@SuppressWarnings("unchecked")
	private List selection = new ArrayList();

	/**
	 * List of open listeners (element type:
	 * <code>ISelectionActivateListener</code>).
	 * 
	 * @see #fireOpen
	 */
	private ListenerList openListeners = new ListenerList();

	public RelTableViewer(Composite parent, int style, URI baseLocation) {
		container = new Composite(parent, style);
		clipboard = new Clipboard(container.getDisplay());
		this.baseLocation = baseLocation;

		menuManager = new MenuManager();
		menuManager.setRemoveAllWhenShown(true);
		menuManager.addMenuListener(this);
		container.setMenu(menuManager.createContextMenu(container));

		dropListener = new DropListener();
		dragListener = new DragListener();
	}

	@Override
	public Control getControl() {
		return container;
	}

	@SuppressWarnings("unchecked")
	public void setSelection(IStructuredSelection selection) {
		List old = this.selection;
		this.selection = new ArrayList(selection.toList());

		for (Object obj : old) {
			Widget w = topicMap.get(obj);

			if (w instanceof LabelEx) {
				if (!this.selection.contains(obj)) {
					((LabelEx) w).setSelected(false);
				}
			}
		}

		for (Object obj : this.selection) {
			Widget w = topicMap.get(obj);

			if (w instanceof LabelEx) {
				((LabelEx) w).setSelected(true);
			}
		}
	}

	@Override
	public ISelection getSelection() {
		return new StructuredSelection(selection);
	}

	public void menuAboutToShow(IMenuManager manager) {
		Point p = container.getDisplay().getCursorLocation();
		final Element elt = getElement(p.x, p.y);

		if (elt != null) {
			final String name = elt.getTagName();

			MenuManager newChild = new MenuManager(
					Messages.getString("RelTabelViewer.menu.new"), "new"); //$NON-NLS-1$ //$NON-NLS-2$
			newChild.add(new GroupMarker("addition")); //$NON-NLS-1$
			manager.add(newChild);

			newChild.add(new Action(
					Messages.getString("RelTabelViewer.menu.new_topicref"), //$NON-NLS-1$
					Descriptor.TOPICREF.getImageDescriptor()) {

				@Override
				public void run() {
				}

			});

			newChild.add(new Action(
					Messages.getString("RelTabelViewer.menu.new_column"), //$NON-NLS-1$
					Descriptor.TOPICREF.getImageDescriptor()) {

				@Override
				public void run() {
				}

			});

			newChild.add(new Action(
					Messages.getString("RelTabelViewer.menu.new_row"), //$NON-NLS-1$
					Descriptor.TOPICREF.getImageDescriptor()) {

				@Override
				public void run() {
				}

			});

			if (DOMUtils.getReference(elt) != null) {
				manager.add(new Action(
						Messages.getString("RelTabelViewer.menu.open")) { //$NON-NLS-1$

					@Override
					public void run() {
						fireOpen(new OpenEvent(RelTableViewer.this,
								new StructuredSelection(elt)));
					}

				});
			}

			manager.add(new Separator("edit")); //$NON-NLS-1$
			if ("topicref".equals(name)) {
				manager.add(new Action(
						Messages.getString("RelTabelViewer.menu.cut"), //$NON-NLS-1$
						Activator.getImageDescriptor(ImageConstants.ICON_CUT)) {

					@Override
					public void run() {
						Node copy = elt.cloneNode(true);

						clipboard.setContents(new Object[] { copy },
								new Transfer[] { NodeTransfer.getInstance() });

						removeChildNode(elt);
					}

				});

				manager.add(new Action(
						Messages.getString("RelTabelViewer.menu.copy"), //$NON-NLS-1$
						Activator.getImageDescriptor(ImageConstants.ICON_COPY)) {

					@Override
					public void run() {
						Node copy = elt.cloneNode(true);

						clipboard.setContents(new Object[] { copy },
								new Transfer[] { NodeTransfer.getInstance() });
					}

				});
			}

			if ("topicref".equals(name) || "relcell".equals(name)) {
				Action pasteAction = new Action(
						Messages.getString("RelTabelViewer.menu.paste"), //$NON-NLS-1$
						Activator.getImageDescriptor(ImageConstants.ICON_PASTE)) {

					@Override
					public void run() {
						Node n = (Node) clipboard.getContents(NodeTransfer.getInstance());

						if (n != null) {
							addChildNode(elt, n);
						}
					}

				};

				Node pasted = (Node) clipboard.getContents(NodeTransfer.getInstance());
				pasteAction.setEnabled(pasted instanceof Element
						&& "topicref".equals(pasted.getLocalName()));
				manager.add(pasteAction);
			}

			if ("topicref".equals(name)) {
				manager.add(new Action(
						Messages.getString("RelTabelViewer.menu.delete_topicref"), //$NON-NLS-1$
						Activator.getImageDescriptor(ImageConstants.ICON_REMOVE)) {

					@Override
					public void run() {
						removeChildNode(elt);
					}

				});
			}

			manager.add(new Action(
					Messages.getString("RelTabelViewer.menu.delete_column"), //$NON-NLS-1$
					Activator.getImageDescriptor(ImageConstants.ICON_REMOVE)) {

				@Override
				public void run() {
					removeColumn(elt);
				}

			});

			if (!"relcolspec".equals(name)) {
				manager.add(new Action(
						Messages.getString("RelTabelViewer.menu.delete_row"), //$NON-NLS-1$
						Activator.getImageDescriptor(ImageConstants.ICON_REMOVE)) {

					@Override
					public void run() {
						removeRow(elt);
					}

				});
			}
		}
	}

	private boolean addChildNode(Element parent, Object child) {
		Document doc = parent.getOwnerDocument();
		Node newChild = null;

		if (child instanceof IResource) {
			IResource res = (IResource) child;

			newChild = doc.createElement("topicref"); //$NON-NLS-1$
			((Element) newChild).setAttribute(
					"href", DOMUtils.getRelativePath(baseLocation, res)); //$NON-NLS-1$
		} else if (child instanceof Node) {
			newChild = doc.importNode((Node) child, true);
		}

		if (newChild != null) {
			parent.appendChild(newChild);
			Composite cell = cellMap.get(parent);
			container.setRedraw(false);
			addTopicRef(cell, newChild);
			container.setRedraw(true);
			container.layout();
			return true;
		} else {
			return false;
		}
	}

	private void removeChildNode(Node child) {
		container.setRedraw(false);
		topicMap.remove(child).dispose();
		child.getParentNode().removeChild(child);
		selection.remove(child);
		container.setRedraw(true);
		container.layout();
	}

	private void removeColumn(Element elt) {
		Element target = getParentCell(elt);

		if (target != null) {
			Element parent = (Element) target.getParentNode();
			NodeList children = parent.getElementsByTagName("relcell");
			int i = 0;

			for (; i < children.getLength(); i++) {
				if (target == children.item(i)) break;
			}

			if (i < children.getLength()) {
				Element reltable = (Element) getInput();
				NodeList headers = reltable.getElementsByTagName("relheader");
				NodeList rows = reltable.getElementsByTagName("relrow");

				for (int r = 0; r < headers.getLength(); r++) {
					Element header = (Element) headers.item(r);
					Element colspec = (Element) header.getElementsByTagName(
							"relcolspec").item(i);
					header.removeChild(colspec);
				}

				for (int r = 0; r < rows.getLength(); r++) {
					Element row = (Element) rows.item(r);
					Element relcell = (Element) row.getElementsByTagName(
							"relcell").item(i);
					row.removeChild(relcell);
					unselect(relcell);
				}

				refresh(reltable, true);
			}
		}
	}

	private void removeRow(Element elt) {
		Element cell = getParentCell(elt);

		if (cell != null) {
			Element row = (Element) cell.getParentNode();
			row.getParentNode().removeChild(row);
			unselect(row);
			refresh(getInput(), true);
		}
	}

	private void unselect(Element elt) {
		if ("topicref".equals(elt.getTagName())) {
			selection.remove(elt);
		} else {
			NodeList children = elt.getChildNodes();
			for (int i = 0; i < children.getLength(); i++) {
				Node child = children.item(i);

				if (child instanceof Element) {
					unselect((Element) child);
				}
			}
		}
	}

	private Element getParentCell(Element elt) {
		if (elt == null) {
			return null;
		} else if ("relcell".equals(elt.getTagName())) {
			return elt;
		} else {
			return getParentCell((Element) elt.getParentNode());
		}
	}

	@Override
	protected void inputChanged(Object input, Object oldInput) {
		refresh(input, false);
	}

	private void refresh(Object input, boolean preserveSelection) {
		container.setRedraw(false);

		try {
			if (!preserveSelection) {
				selection.clear();
			}

			colspecMap.clear();
			cellMap.clear();
			topicMap.clear();
			Control[] children = container.getChildren();

			for (Control child : children) {
				child.dispose();
			}

			if (input != null) {
				RelTableContentProvider cp = (RelTableContentProvider) getContentProvider();
				ILabelProvider lp = (ILabelProvider) getLabelProvider();

				Object[] colspecs = cp.getColSpecs(input);
				GridLayout layout = new GridLayout(colspecs.length, true);
				layout.marginHeight = layout.marginWidth = 0;
				layout.horizontalSpacing = layout.verticalSpacing = 0;
				container.setLayout(layout);

				Display display = container.getShell().getDisplay();
				Color c1 = display.getSystemColor(SWT.COLOR_WIDGET_BACKGROUND);
				Color c2 = display.getSystemColor(SWT.COLOR_WHITE);

				for (Object colspec : colspecs) {
					CLabel label = new CLabel(container, SWT.CENTER);
					label.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
					label.setBackground(new Color[] { c2, c2, c1, c2, c2 },
							new int[] { 10, 50, 90, 100 }, true);
					label.setData(colspec);
					colspecMap.put(colspec, label);
					String text = lp.getText(colspec);

					if (text != null) label.setText(text);
					label.setMenu(menuManager.createContextMenu(label));
				}

				Object[] rows = cp.getRows(input);

				for (Object row : rows) {
					Object[] cells = cp.getCells(row);

					for (Object cell : cells) {
						Composite cc = new Composite(container, SWT.BORDER);
						GridData layoutData = new GridData(GridData.FILL_BOTH);
						layoutData.minimumHeight = 26;
						cc.setLayoutData(layoutData);
						layout = new GridLayout(1, false);
						layout.marginHeight = layout.marginWidth = layout.verticalSpacing = 2;
						cc.setLayout(layout);
						cc.setData(cell);
						cellMap.put(cell, cc);
						Object[] topics = cp.getTopics(cell);

						for (Object topic : topics) {
							addTopicRef(cc, topic);
						}

						cc.setMenu(menuManager.createContextMenu(cc));
						setupDropSupport(cc);
					}
				}
			}
		} finally {
			container.setRedraw(true);
			container.layout();
		}
	}

	private void addTopicRef(Composite cc, final Object topic) {
		ILabelProvider lp = (ILabelProvider) getLabelProvider();
		LabelEx label = new LabelEx(cc);
		label.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		label.setData(topic);
		topicMap.put(topic, label);
		label.setText(lp.getText(topic));
		label.setImage(lp.getImage(topic));
		label.setSelected(selection.contains(topic));

		label.addMouseListener(new MouseListener() {

			public void mouseDoubleClick(MouseEvent e) {
				fireOpen(new OpenEvent(RelTableViewer.this,
						new StructuredSelection(topic)));
			}

			public void mouseDown(MouseEvent e) {
			}

			public void mouseUp(MouseEvent e) {
				if ((e.stateMask & SWT.BUTTON1) != 0) {
					if ((e.stateMask & KEYS) != 0) {
						if (selection.contains(topic)) {
							selection.remove(topic);
						} else {
							selection.add(topic);
						}

						setSelection(new StructuredSelection(selection));
					} else {
						setSelection(new StructuredSelection(topic));
					}
				}
			}
		});

		label.setMenu(menuManager.createContextMenu(label));
		setupDragSupport(label);
		setupDropSupport(label);
	}

	@Override
	public void refresh() {
		inputChanged(getInput(), null);
	}

	@Override
	public void setSelection(ISelection selection, boolean reveal) {
		setSelection(selection);
	}

	@Override
	public void setContentProvider(IContentProvider contentProvider) {
		Assert.isTrue(contentProvider instanceof RelTableContentProvider);
		super.setContentProvider(contentProvider);
	}

	@Override
	public void setLabelProvider(IBaseLabelProvider labelProvider) {
		Assert.isTrue(labelProvider instanceof ILabelProvider);
		super.setLabelProvider(labelProvider);
	}

	/**
	 * Adds a listener for selection-open in this viewer. Has no effect if an
	 * identical listener is already registered.
	 * 
	 * @param listener
	 *            a double-click listener
	 */
	public void addOpenListener(IOpenListener listener) {
		openListeners.add(listener);
	}

	/**
	 * Removes the given open listener from this viewer. Has no affect if an
	 * identical listener is not registered.
	 * 
	 * @param listener
	 *            a double-click listener
	 */
	public void removeOpenListener(IOpenListener listener) {
		openListeners.remove(listener);
	}

	/**
	 * Notifies any open event listeners that a open event has been received.
	 * Only listeners registered at the time this method is called are notified.
	 * 
	 * @param event
	 *            a double-click event
	 * 
	 * @see IOpenListener#open(OpenEvent)
	 */
	protected void fireOpen(final OpenEvent event) {
		Object[] listeners = openListeners.getListeners();
		for (int i = 0; i < listeners.length; ++i) {
			final IOpenListener l = (IOpenListener) listeners[i];
			SafeRunnable.run(new SafeRunnable() {
				public void run() {
					l.open(event);
				}
			});
		}
	}

	private void setupDropSupport(Control control) {
		DropTarget dropTarget = new DropTarget(control, -1);
		dropTarget.setTransfer(new Transfer[] { ResourceTransfer.getInstance(),
				NodeTransfer.getInstance() });
		dropTarget.addDropListener(dropListener);
	}

	private void setupDragSupport(Control control) {
		DragSource dragSource = new DragSource(control, -1);
		dragSource.setTransfer(new Transfer[] { NodeTransfer.getInstance() });
		dragSource.addDragListener(dragListener);
	}

	private Element getElement(int x, int y) {
		for (Composite c : topicMap.values()) {
			Point p = c.toControl(x, y);
			if (c.getBounds().contains(p.x, p.y)) {
				return (Element) c.getData();
			}
		}

		for (Composite c : colspecMap.values()) {
			Point p = c.toControl(x, y);
			if (c.getBounds().contains(p.x, p.y)) {
				return (Element) c.getData();
			}
		}

		for (Composite c : cellMap.values()) {
			Point p = c.toControl(x, y);
			if (c.getBounds().contains(p.x, p.y)) {
				return (Element) c.getData();
			}
		}

		return null;
	}

	class DropListener extends ViewerDropAdapter {

		public DropListener() {
			super(RelTableViewer.this);
		}

		@Override
		public void dragEnter(DropTargetEvent event) {
			if (ResourceTransfer.getInstance().isSupportedType(
					event.currentDataType)
					&& event.detail == DND.DROP_DEFAULT) {
				event.detail = DND.DROP_COPY;
			}

			super.dragEnter(event);
		}

		/**
		 * @see org.eclipse.jface.viewers.ViewerDropAdapter#determineTarget(org.eclipse.swt.dnd.DropTargetEvent)
		 */
		@Override
		protected Object determineTarget(DropTargetEvent event) {
			return ((DropTarget) event.widget).getControl().getData();
		}

		@Override
		public boolean validateDrop(Object target, int operation,
				TransferData transferType) {
			if (target instanceof Element) {
				if (ResourceTransfer.getInstance().isSupportedType(transferType)) {
					return true;
				} else if (NodeTransfer.getInstance().isSupportedType(
						transferType)) {
					return true;
				}
			}

			return false;
		}

		@Override
		public boolean performDrop(Object data) {
			boolean performed = false;
			Element cell = getParentCell((Element) getCurrentTarget());

			if (data instanceof Object[]) {
				for (Object d : (Object[]) data) {
					performed |= addChildNode(cell, d);
				}
			} else {
				performed = addChildNode(cell, data);
			}

			return performed;
		}
	}

	class DragListener implements DragSourceListener {

		public DragListener() {
		}

		public void dragStart(DragSourceEvent event) {
		}

		public void dragFinished(DragSourceEvent event) {
			if (event.doit && event.detail == DND.DROP_MOVE) {
				removeChildNode((Element) ((DragSource) event.widget).getControl().getData());
			}
		}

		public void dragSetData(DragSourceEvent event) {
			if (NodeTransfer.getInstance().isSupportedType(event.dataType)) {
				Element elt = (Element) ((DragSource) event.widget).getControl().getData();
				event.data = elt.cloneNode(true);
			}
		}
	}
}
