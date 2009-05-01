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

import java.io.IOException;
import java.io.StringWriter;
import java.util.HashMap;
import java.util.Map;

import org.apache.xml.serialize.TextSerializer;
import org.dita_op.editor.internal.Activator;
import org.eclipse.core.runtime.IStatus;
import org.eclipse.jface.action.Action;
import org.eclipse.jface.action.IMenuManager;
import org.eclipse.jface.resource.ImageDescriptor;
import org.eclipse.swt.graphics.Image;
import org.eclipse.ui.forms.IDetailsPage;
import org.w3c.dom.Document;
import org.w3c.dom.Element;
import org.w3c.dom.Node;

public abstract class Descriptor {

	private static Map<String, Descriptor> REGISTRY = new HashMap<String, Descriptor>();

	public static Descriptor getDescriptor(Element elt) {
		return REGISTRY.get(elt.getLocalName());
	}

	public static final Descriptor MAP = new MapDescriptor();
	public static final Descriptor TOPICREF = new TopicRefDescriptor();
	public static final Descriptor TOPICGROUP = new TopicGroupDescriptor();
	public static final Descriptor TOPICHEAD = new TopicHeadDescriptor();
	public static final Descriptor NAVREF = new NavRefDescriptor();
	public static final Descriptor ANCHOR = new AnchorDescriptor();
	public static final Descriptor RELTABLE = new RelTableDescriptor();

	public static final Descriptor BOOKMAP = new BookmapDescriptor();
	public static final Descriptor FRONTMATTER = new FrontMatterDescriptor();
	public static final Descriptor CHAPTER = new ChapterDescriptor();
	public static final Descriptor PART = new PartDescriptor();
	public static final Descriptor APPENDIX = new AppendixDescriptor();
	public static final Descriptor BACKMATTER = new BackMatterDescriptor();
	public static final Descriptor BOOKLISTS = new BookListsDescriptor();
	public static final Descriptor NOTICES = new NoticesDescriptor();
	public static final Descriptor DEDICATION = new DedicationDescriptor();
	public static final Descriptor COLOPHON = new ColophonDescriptor();
	public static final Descriptor BOOKABSTRACT = new BookAbstractDescriptor();
	public static final Descriptor DRAFTINTRO = new DraftInfoDescriptor();
	public static final Descriptor PREFACE = new PrefaceDescriptor();
	public static final Descriptor AMENDMENTS = new AmendmentsDescriptor();
	public static final Descriptor TOC = new TOCDescriptor();
	public static final Descriptor FIGURELIST = new FigureListDescriptor();
	public static final Descriptor ABBREVLIST = new AbbrevListDescriptor();
	public static final Descriptor TRADEMARKLIST = new TradeMarkListDescriptor();
	public static final Descriptor GLOSSARYLIST = new GlossaryListDescriptor();
	public static final Descriptor BIBLIOLIST = new BiblioListDescriptor();
	public static final Descriptor INDEXLIST = new IndexListDescriptor();
	public static final Descriptor BOOKLIST = new BookListDescriptor();

	private final String tagName;
	private final String imagePath;

	Descriptor(String tagName, String imagePath) {
		this.tagName = tagName;
		this.imagePath = imagePath;
		REGISTRY.put(tagName, this);
	}

	public String getLabel() {
		return Messages.getString(tagName);
	}

	public boolean isMap() {
		return false;
	}

	public Image getImage() {
		return Activator.getDefault().getImage(imagePath);
	}

	public ImageDescriptor getImageDescriptor() {
		return Activator.getImageDescriptor(imagePath);
	}

	public String getText(Element elt) {
		String id = elt.getAttribute("id"); //$NON-NLS-1$
		return (id != null) ? id : getLabel();
	}

	public boolean instanceOf(Element elt) {
		return tagName.equals(elt.getLocalName());
	}

	public Element createElement(Document document) {
		return document.createElement(tagName);
	}

	public void contributeMenuItems(IMenuManager manager,
			final MapContentProvider modelProvider) {
		final Element selection = modelProvider.getSelection();
		IMenuManager menu = manager.findMenuUsingPath("new_child"); //$NON-NLS-1$
		Descriptor[] children = getChildren();

		for (final Descriptor desc : children) {
			menu.add(new Action(desc.getLabel(), desc.getImageDescriptor()) {

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
					menu.add(new Action(desc.getLabel(),
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

	protected static String toString(Element elt) {
		StringWriter out = new StringWriter();
		TextSerializer serializer = new TextSerializer();
		serializer.setOutputCharStream(out);

		try {
			serializer.serialize(elt);
		} catch (IOException e) {
			Activator.getDefault().log(IStatus.WARNING, e);
		}

		return out.toString();
	}

}
