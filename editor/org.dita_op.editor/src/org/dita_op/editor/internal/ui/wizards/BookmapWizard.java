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

public class BookmapWizard extends AbstractNewFileWizard {

	public BookmapWizard() {
		super("org.dita_op.editor.template.bookmap"); //$NON-NLS-1$
		setTitle(Messages.getString("BookmapWizard.title")); //$NON-NLS-1$
		setDescription(Messages.getString("BookmapWizard.description")); //$NON-NLS-1$
		setDefaultFileName("new_bookmap.ditamap"); //$NON-NLS-1$
		setFileExtensions(new String[] { "ditamap" }); //$NON-NLS-1$
	}
}