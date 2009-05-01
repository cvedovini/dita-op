package org.dita_op.editor.internal.ui.editors.map.model;

public class BookmapDescriptor extends MapDescriptor {

	public BookmapDescriptor() {
		super("bookmap"); //$NON-NLS-1$
	}

	@Override
	protected Descriptor[] getChildren() {
		return new Descriptor[] { Descriptor.FRONTMATTER, Descriptor.CHAPTER,
				Descriptor.PART, Descriptor.APPENDIX, Descriptor.BACKMATTER,
				Descriptor.RELTABLE };
	}

}
