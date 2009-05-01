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

import org.dita_op.editor.internal.Utils;
import org.dita_op.editor.internal.ui.editors.map.model.Descriptor;
import org.eclipse.core.resources.IResource;
import org.eclipse.core.runtime.Platform;
import org.eclipse.core.runtime.content.IContentType;
import org.eclipse.core.runtime.content.IContentTypeManager;
import org.eclipse.jface.viewers.Viewer;
import org.eclipse.jface.viewers.ViewerDropAdapter;
import org.eclipse.swt.dnd.DND;
import org.eclipse.swt.dnd.DropTargetEvent;
import org.eclipse.swt.dnd.TransferData;
import org.eclipse.ui.part.ResourceTransfer;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;

class MSDropListener extends ViewerDropAdapter {

	private final MasterSection section;
	private final IContentTypeManager ctm;
	private final IContentType dmct;

	public MSDropListener(Viewer viewer, MasterSection section) {
		super(viewer);
		this.section = section;
		ctm = Platform.getContentTypeManager();
		dmct = ctm.getContentType("org.dita_op.dita.map"); //$NON-NLS-1$
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

	@Override
	public boolean validateDrop(Object target, int operation,
			TransferData transferType) {
		Element parent = null;

		if (target == null) {
			parent = section.getDocument().getDocumentElement();
		} else if (target instanceof Element) {
			parent = (Element) target;
			int loc = getCurrentLocation();

			if (loc == LOCATION_BEFORE || loc == LOCATION_AFTER) {
				parent = (Element) parent.getParentNode();
			}
		}

		if (parent != null) {
			if (ResourceTransfer.getInstance().isSupportedType(transferType)) {
				return true;
			} else if (NodeTransfer.getInstance().isSupportedType(transferType)) {
				return true;
			}
		}

		return false;
	}

	@Override
	public boolean performDrop(Object data) {
		boolean performed = false;
		Node sibling = null;
		Element parent = (Element) getCurrentTarget();

		if (parent == null) {
			parent = section.getDocument().getDocumentElement();
		} else {
			int loc = getCurrentLocation();

			if (loc == LOCATION_BEFORE || loc == LOCATION_AFTER) {
				sibling = parent;
				parent = (Element) parent.getParentNode();

				if (loc == LOCATION_AFTER) {
					sibling = sibling.getNextSibling();
				}
			}
		}

		Document doc = parent.getOwnerDocument();

		if (data instanceof Object[]) {
			for (Object d : (Object[]) data) {
				performed |= performDrop(doc, parent, sibling, d);
			}
		} else {
			performed = performDrop(doc, parent, sibling, data);
		}

		return performed;
	}

	private boolean performDrop(Document doc, Element parent, Node sibling,
			Object data) {
		Descriptor desc = Descriptor.getDescriptor(parent);
		Node newChild = null;

		if (data instanceof IResource) {
			IResource res = (IResource) data;

			if (isDitamap(res)) {
				newChild = doc.createElement("navref"); //$NON-NLS-1$
				((Element) newChild).setAttribute("mapref", //$NON-NLS-1$
						getRelativePath(res));
			} else {
				newChild = doc.createElement("topicref"); //$NON-NLS-1$
				((Element) newChild).setAttribute("href", getRelativePath(res)); //$NON-NLS-1$
			}
		} else if (data instanceof Node) {
			newChild = doc.importNode((Node) data, true);
		}

		if (newChild != null && desc.accept(newChild)) {
			if (sibling != null) {
				parent.insertBefore(newChild, sibling);
			} else {
				parent.appendChild(newChild);
			}

			return true;
		} else {
			return false;
		}
	}

	private boolean isDitamap(IResource resource) {
		IContentType[] actualContentTypes = ctm.findContentTypesFor(resource.getName());

		for (IContentType actualContentType : actualContentTypes) {
			if (actualContentType.isKindOf(dmct)) {
				return true;
			}
		}

		return false;
	}

	private String getRelativePath(IResource res) {
		URI targetURI = URI.create(res.getFullPath().toString());

		if (section.getBaseLocation() != null) {
			targetURI = Utils.relativize(targetURI, section.getBaseLocation());
		}

		return targetURI.toString();
	}
}