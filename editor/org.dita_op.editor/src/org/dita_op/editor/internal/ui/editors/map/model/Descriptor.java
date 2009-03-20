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

import java.util.HashMap;
import java.util.Map;

import org.dita_op.editor.internal.Activator;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.IMenuManager;
import org.eclipse.jface.resource.ImageDescriptor;
import org.eclipse.swt.graphics.Image;
import org.eclipse.ui.forms.IDetailsPage;
import org.w3c.dom.Element;
import org.w3c.dom.Node;

public abstract class Descriptor {

	private static Map<String, Descriptor> REGISTRY = new HashMap<String, Descriptor>();

	public static Descriptor getDescriptor(Element elt) {
		return REGISTRY.get(elt.getLocalName());
	}

	public static Descriptor MAP = new MapDescriptor();
	public static Descriptor TOPICREF = new TopicRefDescriptor();
	public static Descriptor TOPICGROUP = new TopicGroupDescriptor();
	public static Descriptor TOPICHEAD = new TopicHeadDescriptor();
	public static Descriptor NAVREF = new NavRefDescriptor();
	public static Descriptor ANCHOR = new AnchorDescriptor();
	public static Descriptor RELTABLE = new RelTableDescriptor();

	private final String tagName;
	private final String imagePath;

	Descriptor(String tagName, String imagePath) {
		this.tagName = tagName;
		this.imagePath = imagePath;
		REGISTRY.put(tagName, this);
	}

	public String getTagName() {
		return tagName;
	}

	public Image getImage() {
		return Activator.getDefault().getImage(imagePath);
	}

	public ImageDescriptor getImageDescriptor() {
		return Activator.getImageDescriptor(imagePath);
	}

	public String getText(Element elt) {
		String id = elt.getAttribute("id"); //$NON-NLS-1$
		return (id != null) ? id : elt.getLocalName();
	}

	public boolean instanceOf(Element elt) {
		return tagName.equals(elt.getLocalName());
	}

	public void contributeMenuItems(IMenuManager manager,
			final MapContentProvider modelProvider) {
		final Element selection = modelProvider.getSelection();
		IMenuManager menu = manager.findMenuUsingPath("new_child"); //$NON-NLS-1$
		Descriptor[] children = getChildren();

		for (final Descriptor desc : children) {
			menu.add(new Action(desc.getTagName(), desc.getImageDescriptor()) {

				@Override
				public void run() {
					modelProvider.addChildNode(selection, desc);
				}

			});
		}

		Node parent = selection.getParentNode();
		if (parent instanceof Element) {
			Descriptor pd = getDescriptor((Element) parent);

			if (pd != null) {
				menu = manager.findMenuUsingPath("new_sibling"); //$NON-NLS-1$
				children = pd.getChildren();

				for (final Descriptor desc : children) {
					menu.add(new Action(desc.getTagName(),
							desc.getImageDescriptor()) {

						@Override
						public void run() {
							modelProvider.addSiblingNode(selection, desc);
						}

					});
				}
			}
		}
	}

	public final boolean accept(Node child) {
		if (child instanceof Element) {
			Descriptor[] children = getChildren();

			for (Descriptor desc : children) {
				if (desc.instanceOf((Element) child)) {
					return true;
				}
			}

		}

		return false;
	}

	protected abstract Descriptor[] getChildren();

	public abstract IDetailsPage getDetailsPage();
}
