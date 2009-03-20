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
import org.dita_op.editor.internal.ui.editors.map.pages.TopicrefDetails;
import org.eclipse.ui.forms.IDetailsPage;
import org.w3c.dom.Element;

class TopicRefDescriptor extends Descriptor {

	TopicRefDescriptor() {
		super("topicref", ImageConstants.ICON_TOPIC); //$NON-NLS-1$
	}

	@Override
	public String getText(Element elt) {
		String title = elt.getAttribute("navtitle"); //$NON-NLS-1$
		String href = elt.getAttribute("href"); //$NON-NLS-1$

		if (title != null) {
			return title;
		} else if (href != null) {
			return href;
		}

		return super.getText(elt);
	}

	@Override
	protected Descriptor[] getChildren() {
		return new Descriptor[] { Descriptor.TOPICREF, Descriptor.TOPICHEAD,
				Descriptor.TOPICGROUP, Descriptor.NAVREF, Descriptor.ANCHOR };
	}

	@Override
	public IDetailsPage getDetailsPage() {
		return new TopicrefDetails();
	}

}
