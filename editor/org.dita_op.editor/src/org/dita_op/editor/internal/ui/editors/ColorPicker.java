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
package org.dita_op.editor.internal.ui.editors;

import java.util.ArrayList;
import java.util.List;

import org.dita_op.editor.internal.utils.RGBUtils;
import org.eclipse.jface.preference.ColorSelector;
import org.eclipse.jface.util.IPropertyChangeListener;
import org.eclipse.jface.util.PropertyChangeEvent;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.events.SelectionListener;
import org.eclipse.swt.graphics.RGB;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Event;

public class ColorPicker extends Composite {

	private Button button;
	private ColorSelector selector;
	private final List<SelectionListener> listeners = new ArrayList<SelectionListener>();

	public ColorPicker(Composite parent, int style) {
		super(parent, style);
		setLayout(new GridLayout(2, false));

		button = new Button(this, SWT.CHECK);
		button.addSelectionListener(new SelectionAdapter() {

			/**
			 * @see org.eclipse.swt.events.SelectionAdapter#widgetSelected(org.eclipse.swt.events.SelectionEvent)
			 */
			@Override
			public void widgetSelected(SelectionEvent e) {
				boolean enabled = button.getSelection();
				selector.setEnabled(enabled);
				notifyListener();
			}
		});

		selector = new ColorSelector(this);
		selector.addListener(new IPropertyChangeListener() {

			public void propertyChange(PropertyChangeEvent event) {
				if (selector.getButton().isEnabled()) {
					notifyListener();
				}
			}
		});
	}

	public void setText(String text) {
		button.setText(text);
	}

	public RGB getSelection() {
		if (button.getSelection()) {
			return selector.getColorValue();
		} else {
			return null;
		}
	}

	public void setSelection(RGB colorValue) {
		if (colorValue == null) {
			button.setSelection(false);
			selector.setEnabled(false);
			selector.setColorValue(RGBUtils.SILVER);
		} else {
			button.setSelection(true);
			selector.setEnabled(true);
			selector.setColorValue(colorValue);
		}
	}

	void addSelectionListener(SelectionListener listener) {
		listeners.add(listener);
	}

	public void removeSelectionListener(SelectionListener listener) {
		listeners.remove(listener);
	}

	private void notifyListener() {
		Event event = new Event();
		event.widget = this;
		SelectionEvent e = new SelectionEvent(event);

		for (SelectionListener listener : listeners) {
			listener.widgetSelected(e);
		}
	}

}
