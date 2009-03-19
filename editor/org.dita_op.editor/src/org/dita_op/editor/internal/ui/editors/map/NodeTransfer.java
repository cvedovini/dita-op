package org.dita_op.editor.internal.ui.editors.map;

import org.eclipse.swt.dnd.ByteArrayTransfer;
import org.eclipse.swt.dnd.DND;
import org.eclipse.swt.dnd.TransferData;
import org.w3c.dom.Node;

class NodeTransfer extends ByteArrayTransfer {

	private static NodeTransfer instance = new NodeTransfer();
	private static final String TYPE_NAME = NodeTransfer.class.getName();
	private static final int TYPEID = registerType(TYPE_NAME);

	public static NodeTransfer getInstance() {
		return instance;
	}

	private Node node;

	private NodeTransfer() {
	}

	protected int[] getTypeIds() {
		return new int[] { TYPEID };
	}

	protected String[] getTypeNames() {
		return new String[] { TYPE_NAME };
	}

	protected void javaToNative(Object object, TransferData transferData) {
		if (object instanceof Node) {
			node = (Node) object;
			super.javaToNative(TYPE_NAME.getBytes(), transferData);
		} else {
			node = null;
			DND.error(DND.ERROR_INVALID_DATA);
		}
	}

	protected Object nativeToJava(TransferData transferData) {
		return node;
	}

}