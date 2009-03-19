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

import java.util.List;

import org.dita_op.editor.internal.utils.RGBUtils;
import org.xml.sax.ContentHandler;
import org.xml.sax.SAXException;
import org.xml.sax.ext.LexicalHandler;
import org.xml.sax.helpers.AttributesImpl;

public class ProfileSerializer {

	private static final String CDATA = "CDATA"; //$NON-NLS-1$
	private static final String EMPTY = ""; //$NON-NLS-1$

	private final ContentHandler handler;
	private final LexicalHandler lexicalHandler;

	public ProfileSerializer(ContentHandler handler) {
		this.handler = handler;

		if (handler instanceof LexicalHandler) {
			this.lexicalHandler = (LexicalHandler) handler;
		} else {
			this.lexicalHandler = null;
		}
	}

	public void serialized(ProfileModel model) throws SAXException {
		handler.startDocument();
/*
		if (lexicalHandler != null) {
			lexicalHandler.startDTD("val", null, "ditaval.dtd"); //$NON-NLS-1$ //$NON-NLS-2$
			lexicalHandler.endDTD();
		}
*/
		serialize(model.getVal());

		handler.endDocument();
	}

	private void serialize(Val val) throws SAXException {
		handler.startElement(EMPTY, EMPTY, "val", null); //$NON-NLS-1$

		if (val.getBackgroundConflictColor() != null
				|| val.getForegroundConflictColor() != null) {
			AttributesImpl atts = new AttributesImpl();

			if (val.getForegroundConflictColor() != null) {
				atts.addAttribute(EMPTY, EMPTY,
						"foreground-conflict-color", //$NON-NLS-1$
						CDATA,
						RGBUtils.toString(val.getForegroundConflictColor()));
			}

			if (val.getBackgroundConflictColor() != null) {
				atts.addAttribute(EMPTY, EMPTY,
						"background-conflict-color", //$NON-NLS-1$
						CDATA,
						RGBUtils.toString(val.getBackgroundConflictColor()));
			}

			handler.startElement(EMPTY, EMPTY, "style-conflict", atts); //$NON-NLS-1$
			handler.endElement(EMPTY, EMPTY, "style-conflict"); //$NON-NLS-1$
		}

		List<AbstractProp> props = val.getProps();

		if (props != null) {
			for (AbstractProp p : props) {
				if (p instanceof Prop) {
					serialize((Prop) p);
				} else {
					serialize((Revprop) p);
				}
			}
		}

		handler.endElement(EMPTY, EMPTY, "val"); //$NON-NLS-1$
	}

	private void addAttributes(AbstractProp p, AttributesImpl atts) {
		atts.addAttribute(EMPTY, EMPTY, "action", CDATA, p.getAction()); //$NON-NLS-1$

		if (p.getValue() != null) {
			atts.addAttribute(EMPTY, EMPTY, "val", CDATA, p.getValue()); //$NON-NLS-1$
		}

		if (p.getStyle() != null) {
			atts.addAttribute(EMPTY, EMPTY, "style", CDATA, p.getStyle()); //$NON-NLS-1$
		}

		if (p.getColor() != null) {
			atts.addAttribute(EMPTY, EMPTY, "color", CDATA, //$NON-NLS-1$
					RGBUtils.toString(p.getColor()));
		}

		if (p.getBackColor() != null) {
			atts.addAttribute(EMPTY, EMPTY, "backcolor", CDATA, //$NON-NLS-1$
					RGBUtils.toString(p.getBackColor()));
		}
	}

	private void serialize(Prop p) throws SAXException {
		AttributesImpl atts = new AttributesImpl();
		addAttributes(p, atts);

		if (p.getAttribute() != null) {
			atts.addAttribute(EMPTY, EMPTY, "att", CDATA, p.getAttribute()); //$NON-NLS-1$
		}

		handler.startElement(EMPTY, EMPTY, "prop", atts); //$NON-NLS-1$
		serializeFlags(p);
		handler.endElement(EMPTY, EMPTY, "prop"); //$NON-NLS-1$
	}

	private void serialize(Revprop p) throws SAXException {
		AttributesImpl atts = new AttributesImpl();
		addAttributes(p, atts);

		if (p.getChangeBar() != null) {
			atts.addAttribute(EMPTY, EMPTY, "changebar", CDATA, //$NON-NLS-1$
					p.getChangeBar());
		}

		handler.startElement(EMPTY, EMPTY, "revprop", atts); //$NON-NLS-1$
		serializeFlags(p);
		handler.endElement(EMPTY, EMPTY, "revprop"); //$NON-NLS-1$
	}

	private void serializeFlags(AbstractProp p) throws SAXException {
		serializeFlag("startflag", p.getStartFlag()); //$NON-NLS-1$
		serializeFlag("endflag", p.getEndFlag()); //$NON-NLS-1$
	}

	private void serializeFlag(String name, Flag f) throws SAXException {
		if (f != null) {
			String imageRef = f.getImageRef();
			String alttext = f.getAltText();

			if (imageRef != null && alttext != null) {
				AttributesImpl atts = new AttributesImpl();

				if (imageRef != null) {
					atts.addAttribute(EMPTY, EMPTY, "imageref", CDATA, imageRef); //$NON-NLS-1$
				}

				handler.startElement(EMPTY, EMPTY, name, atts);

				if (alttext != null) {
					handler.startElement(EMPTY, EMPTY, "alt-text", null); //$NON-NLS-1$
					handler.characters(alttext.toCharArray(), 0,
							alttext.length());
					handler.endElement(EMPTY, EMPTY, "alt-text"); //$NON-NLS-1$
				}

				handler.endElement(EMPTY, EMPTY, name);
			}
		}
	}
}
