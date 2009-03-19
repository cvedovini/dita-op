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
package org.dita_op.editor.internal.ui.editors.profile.model;

import org.dita_op.editor.internal.utils.RGBUtils;
import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.ext.DefaultHandler2;

class ProfileHandler extends DefaultHandler2 {

	private final ProfileModel model;
	private AbstractProp currentProp;
	private Flag currentFlag;
	private StringBuilder buffer = new StringBuilder();

	public ProfileHandler(ProfileModel model) {
		this.model = model;
	}

	/**
	 * @see org.xml.sax.helpers.DefaultHandler#startElement(java.lang.String,
	 *      java.lang.String, java.lang.String, org.xml.sax.Attributes)
	 */
	@Override
	public void startElement(String uri, String localName, String name,
			Attributes attributes) throws SAXException {
		if ("style-conflict".equals(name)) { //$NON-NLS-1$
			model.getVal().setForegroundConflictColor(
					RGBUtils.parse(attributes.getValue("foreground-conflict-color"))); //$NON-NLS-1$
			model.getVal().setBackgroundConflictColor(
					RGBUtils.parse(attributes.getValue("background-conflict-color"))); //$NON-NLS-1$
		} else if ("prop".equals(name)) { //$NON-NLS-1$
			Prop p = new Prop();
			p.setAttribute(attributes.getValue("att")); //$NON-NLS-1$
			p.setValue(attributes.getValue("val")); //$NON-NLS-1$
			p.setAction(attributes.getValue("action")); //$NON-NLS-1$
			p.setStyle(attributes.getValue("style")); //$NON-NLS-1$
			p.setColor(RGBUtils.parse(attributes.getValue("color"))); //$NON-NLS-1$
			p.setBackColor(RGBUtils.parse(attributes.getValue("backcolor"))); //$NON-NLS-1$
			model.getVal().getProps().add(p);
			currentProp = p;
		} else if ("revprop".equals(name)) { //$NON-NLS-1$
			Revprop p = new Revprop();
			p.setChangeBar(attributes.getValue("changebar")); //$NON-NLS-1$
			p.setValue(attributes.getValue("val")); //$NON-NLS-1$
			p.setAction(attributes.getValue("action")); //$NON-NLS-1$
			p.setStyle(attributes.getValue("style")); //$NON-NLS-1$
			p.setColor(RGBUtils.parse(attributes.getValue("color"))); //$NON-NLS-1$
			p.setBackColor(RGBUtils.parse(attributes.getValue("backcolor"))); //$NON-NLS-1$
			model.getVal().getProps().add(p);
			currentProp = p;
		} else if (currentProp != null && "startflag".equals(name)) { //$NON-NLS-1$
			Flag flag = new Flag();
			flag.setImageRef(attributes.getValue("imageref")); //$NON-NLS-1$
			currentProp.setStartFlag(flag);
			currentFlag = flag;
		} else if (currentProp != null && "endflag".equals(name)) { //$NON-NLS-1$
			Flag flag = new Flag();
			flag.setImageRef(attributes.getValue("imageref")); //$NON-NLS-1$
			currentProp.setEndFlag(flag);
			currentFlag = flag;
		}
	}

	/**
	 * @see org.xml.sax.helpers.DefaultHandler#characters(char[], int, int)
	 */
	@Override
	public void characters(char[] ch, int start, int length)
			throws SAXException {
		buffer.append(ch, start, length);
	}

	/**
	 * @see org.xml.sax.helpers.DefaultHandler#endElement(java.lang.String,
	 *      java.lang.String, java.lang.String)
	 */
	@Override
	public void endElement(String uri, String localName, String name)
			throws SAXException {
		if ("prop".equals(name) || "revprop".equals(name)) { //$NON-NLS-1$ //$NON-NLS-2$
			currentProp = null;
		} else if ("startflag".equals(name) || "endflag".equals(name)) { //$NON-NLS-1$ //$NON-NLS-2$
			currentFlag = null;
		} else if (currentFlag != null && "alt-text".equals(name)) { //$NON-NLS-1$
			currentFlag.setAltText(buffer.toString());
		}

		buffer.setLength(0);
	}
}