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
package org.dita_op.editor.internal.ui.editors.map.pages;

import org.eclipse.jface.viewers.IContentProvider;
import org.eclipse.jface.viewers.Viewer;
import org.w3c.dom.Element;
import org.w3c.dom.NodeList;

class RelTableContentProvider implements IContentProvider {

	public void dispose() {
	}

	public void inputChanged(Viewer viewer, Object oldInput, Object newInput) {
		if (newInput != null) {
			normalize((Element) newInput);
		}
	}

	private void normalize(Element input) {
		// First, let's count the number of columns
		// There should be at least one column
		int colnum = 1;
		NodeList headers = input.getElementsByTagName("relheader");
		Element header = null;

		// Only one relheader, optional.
		if (headers.getLength() > 0) {
			header = (Element) headers.item(0);
			NodeList colspecs = header.getElementsByTagName("relcolspec");
			colnum = Math.max(colnum, colspecs.getLength());
		}

		NodeList rows = input.getElementsByTagName("relrow");

		for (int i = 0; i < rows.getLength(); i++) {
			Element row = (Element) rows.item(i);
			NodeList relcells = row.getElementsByTagName("relcell");
			colnum = Math.max(colnum, relcells.getLength());
		}

		// Then add missing relcolspec and missing relcell if any
		// Make sure there's a header
		if (header == null) {
			header = input.getOwnerDocument().createElement("relheader");
			input.insertBefore(header, input.getFirstChild());
		}

		// Add missing relcolspec
		NodeList colspecs = header.getElementsByTagName("relcolspec");

		if (colspecs.getLength() < colnum) {
			int diff = colnum - colspecs.getLength();

			for (int i = 0; i < diff; i++) {
				Element colspec = input.getOwnerDocument().createElement(
						"relcolspec");
				header.appendChild(colspec);
			}
		}

		// Add at least one row
		if (rows.getLength() == 0) {
			Element row = input.getOwnerDocument().createElement("relrow");
			input.appendChild(row);
			rows = input.getElementsByTagName("relrow");
		}

		// Then add missing relcell
		for (int i = 0; i < rows.getLength(); i++) {
			Element row = (Element) rows.item(i);
			NodeList relcells = row.getElementsByTagName("relcell");

			if (relcells.getLength() < colnum) {
				int diff = colnum - relcells.getLength();

				for (int j = 0; j < diff; j++) {
					Element cell = input.getOwnerDocument().createElement(
							"relcell");
					row.appendChild(cell);
				}
			}

		}
	}

	public Object[] getColSpecs(Object inputElement) {
		Element rt = (Element) inputElement;
		NodeList headers = rt.getElementsByTagName("relheader");

		Element header = (Element) headers.item(0);
		NodeList colspecs = header.getElementsByTagName("relcolspec");
		Object[] result = new Object[colspecs.getLength()];

		for (int i = 0; i < result.length; i++) {
			result[i] = colspecs.item(i);
		}

		return result;
	}

	public Object[] getRows(Object inputElement) {
		Element rt = (Element) inputElement;
		NodeList rows = rt.getElementsByTagName("relrow");
		Object[] result = new Object[rows.getLength()];

		for (int i = 0; i < result.length; i++) {
			result[i] = rows.item(i);
		}

		return result;
	}

	public Object[] getCells(Object object) {
		Element rt = (Element) object;
		NodeList cells = rt.getElementsByTagName("relcell");
		Object[] result = new Object[cells.getLength()];

		for (int i = 0; i < result.length; i++) {
			result[i] = cells.item(i);
		}

		return result;
	}

	public Object[] getTopics(Object object) {
		Element rt = (Element) object;
		NodeList topics = rt.getElementsByTagName("topicref");
		Object[] result = new Object[topics.getLength()];

		for (int i = 0; i < result.length; i++) {
			result[i] = topics.item(i);
		}

		return result;
	}
}
