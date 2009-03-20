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
package org.dita_op.editor.internal.ui.editors.map;

import org.dita_op.editor.internal.ui.editors.map.model.Descriptor;
import org.dita_op.editor.internal.ui.editors.map.model.MapContentProvider;
import org.eclipse.swt.dnd.DND;
import org.eclipse.swt.dnd.DragSourceEvent;
import org.eclipse.swt.dnd.DragSourceListener;
import org.w3c.dom.Element;
import org.w3c.dom.Node;

class MSDragSourceListener implements DragSourceListener {

	private final MapContentProvider modelProvider;

	MSDragSourceListener(MapContentProvider modelProvider) {
		this.modelProvider = modelProvider;
	}

	public void dragStart(DragSourceEvent event) {
		Element elt = modelProvider.getSelection();
		event.doit = Descriptor.MAP.instanceOf(elt);
	}

	public void dragFinished(DragSourceEvent event) {
		if (event.doit && event.detail == DND.DROP_MOVE) {
			modelProvider.remove(modelProvider.getSelection());
		}
	}

	public void dragSetData(DragSourceEvent event) {
		if (NodeTransfer.getInstance().isSupportedType(event.dataType)) {
			Node orig = modelProvider.getSelection();
			event.data = orig.cloneNode(true);
		}
	}

}