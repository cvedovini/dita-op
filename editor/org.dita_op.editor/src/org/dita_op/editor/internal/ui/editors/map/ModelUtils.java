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

import org.eclipse.swt.SWT;
import org.eclipse.swt.widgets.Combo;
import org.eclipse.swt.widgets.Text;
import org.w3c.dom.Element;

class ModelUtils {

	public static final String BLANK = ""; //$NON-NLS-1$

	public static final String UNSPECIFIED = "<unspecified>"; //$NON-NLS-1$

	public static final String USE_CONREF_TARGET = "<-use-conref-target>"; //$NON-NLS-1$

	public static void loadText(Element model, Text control, String attribute) {
		String text = model.getAttribute(attribute);
		control.setText(text == null ? BLANK : text);
	}

	public static void loadFile(Element model, FileChooser control,
			String attribute) {
		String text = model.getAttribute(attribute);
		control.setText(text == null ? BLANK : text);
	}

	public static void loadCombo(Element model, Combo control, String attribute) {
		String text = model.getAttribute(attribute);

		if (text == null) {
			if ((control.getStyle() & SWT.READ_ONLY) == SWT.READ_ONLY) {
				control.setText(UNSPECIFIED);
			} else {
				control.setText(BLANK);
			}
		} else if ("-dita-use-conref-target".equals(text)) { //$NON-NLS-1$
			control.setText(USE_CONREF_TARGET);
		} else {
			control.setText(text);
		}
	}

	public static void saveText(Element model, Text control, String attribute) {
		String text = control.getText().trim();

		if (BLANK.equals(text)) {
			model.removeAttribute(attribute);
		} else {
			model.setAttribute(attribute, text);
		}
	}

	public static void saveFile(Element model, FileChooser control,
			String attribute) {
		String text = control.getText().trim();

		if (BLANK.equals(text)) {
			model.removeAttribute(attribute);
		} else {
			model.setAttribute(attribute, text);
		}
	}

	public static void saveCombo(Element model, Combo control, String attribute) {
		String text = control.getText().trim();

		if (BLANK.equals(text) || UNSPECIFIED.equals(text)) {
			model.removeAttribute(attribute);
		} else if (USE_CONREF_TARGET.equals(text)) {
			model.setAttribute(attribute, "-dita-use-conref-target"); //$NON-NLS-1$
		} else {
			model.setAttribute(attribute, text);
		}
	}

}
