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
package org.dita_op.editor.internal.ui.editors.map.model;

import org.dita_op.editor.internal.ImageConstants;
import org.dita_op.editor.internal.ui.editors.map.pages.MapDetails;
import org.eclipse.ui.forms.IDetailsPage;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

public class MapDescriptor extends Descriptor {

	MapDescriptor() {
		super("map", ImageConstants.ICON_DITAMAP); //$NON-NLS-1$
	}

	protected MapDescriptor(String tagName) {
		super(tagName, ImageConstants.ICON_DITAMAP);
	}

	@Override
	public boolean isMap() {
		return true;
	}

	@Override
	public String getText(Element elt) {
		String title = getTitle(elt);
		return (title != null) ? title : super.getText(elt);
	}

	@Override
	protected Descriptor[] getChildren() {
		return new Descriptor[] { Descriptor.TOPICREF, Descriptor.TOPICHEAD,
				Descriptor.TOPICGROUP, Descriptor.NAVREF, Descriptor.ANCHOR,
				Descriptor.RELTABLE };
	}

	@Override
	public IDetailsPage getDetailsPage() {
		return new MapDetails();
	}

	public static String getTitle(Element elt) {
		NodeList nl = elt.getElementsByTagName("title"); //$NON-NLS-1$

		if (nl.getLength() > 0) {
			return toString((Element) nl.item(0));
		}

		return elt.getAttribute("title"); //$NON-NLS-1$;
	}

	public static void setTitle(Element elt, String title) {
		if (title == null) {
			removeTitle(elt);
		} else {
			elt.setAttribute("title", title); //$NON-NLS-1$
		}

		// Make sure to remove "title" attribute if any, they are deprecated!
		elt.removeAttribute("title"); //$NON-NLS-1$ 
		NodeList nl = elt.getElementsByTagName("title"); //$NON-NLS-1$

		Document doc = elt.getOwnerDocument();
		Element newChild = doc.createElement("title"); //$NON-NLS-1$
		newChild.appendChild(doc.createTextNode(title));

		if (nl.getLength() > 0) {
			elt.replaceChild(newChild, nl.item(0));
		} else {
			elt.insertBefore(newChild, elt.getFirstChild());
		}
	}

	public static void removeTitle(Element elt) {
		elt.removeAttribute("title"); //$NON-NLS-1$
		NodeList nl = elt.getElementsByTagName("title"); //$NON-NLS-1$

		for (int i = 0; i < nl.getLength(); i++) {
			elt.removeChild(nl.item(i));
		}
	}

}
