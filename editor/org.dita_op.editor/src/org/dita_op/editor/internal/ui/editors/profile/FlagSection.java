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
package org.dita_op.editor.internal.ui.editors.profile;

import java.util.ArrayList;
import java.util.List;

import org.dita_op.editor.internal.ui.editors.profile.model.Flag;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.ModifyEvent;
import org.eclipse.swt.events.ModifyListener;
import org.eclipse.swt.events.SelectionAdapter;
import org.eclipse.swt.events.SelectionEvent;
import org.eclipse.swt.layout.GridData;
import org.eclipse.swt.layout.GridLayout;
import org.eclipse.swt.widgets.Button;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Event;
import org.eclipse.swt.widgets.FileDialog;
import org.eclipse.swt.widgets.Text;
import org.eclipse.ui.forms.widgets.FormToolkit;
import org.eclipse.ui.forms.widgets.Section;

class FlagSection {

	private static final String BLANK = ""; //$NON-NLS-1$
	private Text imageRefText;
	private Text altTextText;
	private List<ModifyListener> listeners = new ArrayList<ModifyListener>();
	private ModifyListener internalListener = new ModifyListener() {

		public void modifyText(ModifyEvent e) {
			notifyModifyListeners();
		}
	};
	private Section section;

	public FlagSection(Composite parent, FormToolkit toolkit) {
		section = toolkit.createSection(parent, Section.TITLE_BAR
				| Section.CLIENT_INDENT | Section.COMPACT | Section.TWISTIE);

		Composite container = toolkit.createComposite(section);
		container.setLayoutData(new GridData(GridData.FILL_BOTH));

		createClientArea(container, toolkit);
		section.setClient(container);
	}

	protected void createClientArea(Composite container, FormToolkit toolkit) {
		container.setLayout(new GridLayout(3, false));

		toolkit.createLabel(container,
				Messages.getString("FlagSection.imageref.label")); //$NON-NLS-1$
		imageRefText = toolkit.createText(container, BLANK);
		imageRefText.setLayoutData(new GridData(GridData.FILL_HORIZONTAL));
		imageRefText.addModifyListener(internalListener);

		Button browseButton = toolkit.createButton(container,
				Messages.getString("FlagSection.browse.button"), SWT.NONE); //$NON-NLS-1$
		browseButton.addSelectionListener(new SelectionAdapter() {

			/**
			 * @see org.eclipse.swt.events.SelectionAdapter#widgetSelected(org.eclipse.swt.events.SelectionEvent)
			 */
			@Override
			public void widgetSelected(SelectionEvent e) {
				handleBrowseButton();
			}
		});

		toolkit.createLabel(container,
				Messages.getString("FlagSection.alttext.label")); //$NON-NLS-1$
		altTextText = toolkit.createText(container, BLANK);
		GridData data = new GridData(GridData.FILL_HORIZONTAL);
		data.horizontalSpan = 2;
		altTextText.setLayoutData(data);
		altTextText.addModifyListener(internalListener);
	}

	public Section getSection() {
		return section;
	}

	public void setText(String text) {
		section.setText(text);
	}

	public void setLayoutData(Object layoutData) {
		section.setLayoutData(layoutData);
	}

	public void setFlag(Flag flag) {
		if (flag != null) {
			if (flag.getImageRef() != null) {
				imageRefText.setText(flag.getImageRef());
			} else {
				imageRefText.setText(BLANK);
			}

			if (flag.getAltText() != null) {
				altTextText.setText(flag.getAltText());
			} else {
				altTextText.setText(BLANK);
			}
		} else {
			imageRefText.setText(BLANK);
			altTextText.setText(BLANK);
		}
	}

	public Flag getFlag() {
		String imageRef = imageRefText.getText().trim();
		String altText = altTextText.getText().trim();

		if (imageRef.length() > 0 || altText.length() > 0) {
			Flag flag = new Flag();

			if (imageRef.length() > 0) {
				flag.setImageRef(imageRef);
			}

			if (altText.length() > 0) {
				flag.setAltText(altText);
			}

			return flag;
		} else {
			return null;
		}
	}

	public void addModifyListener(ModifyListener listener) {
		listeners.add(listener);
	}

	public void removeModifyListener(ModifyListener listener) {
		listeners.remove(listener);
	}

	private void notifyModifyListeners() {
		Event e = new Event();
		e.widget = section;
		ModifyEvent event = new ModifyEvent(e);

		for (ModifyListener listener : listeners) {
			listener.modifyText(event);
		}
	}

	private void handleBrowseButton() {
		FileDialog dialog = new FileDialog(section.getShell(), SWT.OPEN);
		dialog.setFileName(imageRefText.getText());
		String result = dialog.open();

		if (result != null) {
			imageRefText.setText(result);
		}
	}
}
