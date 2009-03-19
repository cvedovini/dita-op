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
package org.dita_op.editor.internal.ui.editors.map;

import org.eclipse.wst.xml.ui.internal.tabletree.IDesignViewer;
import org.eclipse.wst.xml.ui.internal.tabletree.XMLMultiPageEditorPart;

@SuppressWarnings("restriction")//$NON-NLS-1$
public class MapEditor extends XMLMultiPageEditorPart {

	/**
	 * Creates a multi-page sourceEditor example.
	 */
	public MapEditor() {
		super();
	}

	/**
	 * @see org.eclipse.wst.xml.ui.internal.tabletree.XMLMultiPageEditorPart#createDesignPage()
	 */
	@Override
	protected IDesignViewer createDesignPage() {
		return new DesignPage(getContainer());
	}

}
