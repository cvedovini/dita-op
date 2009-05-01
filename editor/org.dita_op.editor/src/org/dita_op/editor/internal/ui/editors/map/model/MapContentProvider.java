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
package org.dita_op.editor.internal.ui.editors.map.model;

import java.util.ArrayList;
import java.util.List;

import org.eclipse.jface.viewers.ILabelProvider;
import org.eclipse.jface.viewers.ILabelProviderListener;
import org.eclipse.jface.viewers.IStructuredSelection;
import org.eclipse.jface.viewers.ITreeContentProvider;
import org.eclipse.jface.viewers.StructuredSelection;
import org.eclipse.jface.viewers.TreeViewer;
import org.eclipse.jface.viewers.Viewer;
import org.eclipse.swt.graphics.Image;
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
import org.w3c.dom.Element;
import org.w3c.dom.Node;
import org.w3c.dom.NodeList;

@SuppressWarnings( { "restriction" })//$NON-NLS-1$
public class MapContentProvider implements ITreeContentProvider,
		ILabelProvider, CMDocumentManagerListener {

	public MapContentProvider() {
	}

	private TreeViewer viewer;
	private Document document;

	public Document getDocument() {
		return document;
	}

	public Element getSelection() {
		IStructuredSelection selection = (IStructuredSelection) viewer.getSelection();
		Element elt = (Element) selection.getFirstElement();
		return (elt != null) ? elt : document.getDocumentElement();
	}

	public void addChildNode(Node parent, Node n) {
		n = document.importNode(n, true);
		parent.appendChild(n);

		viewer.add(parent, n);
		viewer.setSelection(new StructuredSelection(n), true);
	}

	public void addChildNode(Node parent, Descriptor desc) {
		Element child = desc.createElement(document);
		parent.appendChild(child);

		viewer.add(parent, child);
		viewer.setSelection(new StructuredSelection(child), true);
	}

	public void addSiblingNode(Node sibling, Descriptor desc) {
		Node parent = sibling.getParentNode();

		Element child = desc.createElement(document);
		Node anchor = sibling.getNextSibling();

		if (anchor == null) {
			parent.appendChild(child);
		} else {
			parent.insertBefore(child, anchor);
		}

		viewer.add(parent, child);
		viewer.setSelection(new StructuredSelection(child), true);
	}

	public void remove(Node n) {
		n.getParentNode().removeChild(n);
		viewer.remove(n);
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

	public void dispose() {
		unlisten();
	}

	public Image getImage(Object element) {
		Descriptor desc = Descriptor.getDescriptor((Element) element);
		return (desc != null) ? desc.getImage() : null;
	}

	public String getText(Object element) {
		Descriptor desc = Descriptor.getDescriptor((Element) element);
		return (desc != null) ? desc.getText((Element) element)
				: element.toString();
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

	public void cacheUpdated(CMDocumentCache cache, String uri, int oldStatus,
			int newStatus, CMDocument cmDocument) {
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

			if (child instanceof Element) {
				Descriptor desc = Descriptor.getDescriptor((Element) child);

				if (desc != null) {
					children.add(child);
				}
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

	private void doDelayedRefreshForViewers() {
		if ((viewer != null) && !viewer.getControl().isDisposed()) {
			viewer.getControl().getDisplay().asyncExec(new Runnable() {
				public void run() {
					if ((viewer != null) && !viewer.getControl().isDisposed()) {
						viewer.refresh(true);
					}
				}
			});
		}
	}

}