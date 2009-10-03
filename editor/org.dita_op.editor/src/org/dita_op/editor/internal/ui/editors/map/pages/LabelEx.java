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
package org.dita_op.editor.internal.ui.editors.map.pages;

import org.dita_op.editor.internal.Activator;
import org.eclipse.swt.SWT;
import org.eclipse.swt.events.PaintEvent;
import org.eclipse.swt.events.PaintListener;
import org.eclipse.swt.graphics.Color;
import org.eclipse.swt.graphics.GC;
import org.eclipse.swt.graphics.Image;
import org.eclipse.swt.graphics.Point;
import org.eclipse.swt.graphics.Rectangle;
import org.eclipse.swt.widgets.Canvas;
import org.eclipse.swt.widgets.Composite;
import org.eclipse.swt.widgets.Display;

/**
 * Shamelessly inspired by the org.eclipse.swt.custom.CLabel class.
 */
class LabelEx extends Canvas {

	/** Gap between icon and text */
	private static final int GAP = 5;

	/** Left and right margins */
	private static final int INDENT = 3;
	private int hIndent = INDENT;
	private int vIndent = INDENT;

	/** a string inserted in the middle of text that has been shortened */
	private static final String ELLIPSIS = "..."; //$NON-NLS-1$ // could use the ellipsis glyph on some platforms "\u2026"

	private static int DRAW_FLAGS = SWT.DRAW_TAB | SWT.DRAW_TRANSPARENT
			| SWT.DRAW_DELIMITER;

	/** the current text */
	private String text;

	/** the current icon */
	private Image image;

	private boolean selected = false;

	public LabelEx(Composite parent) {
		super(parent, SWT.DOUBLE_BUFFERED);

		addPaintListener(new PaintListener() {
			public void paintControl(PaintEvent event) {
				onPaint(event);
			}
		});
	}

	/**
	 * @return the selected
	 */
	public boolean isSelected() {
		return selected;
	}

	/**
	 * @param selected
	 *            the selected to set
	 */
	public void setSelected(boolean selected) {
		this.selected = selected;
		redraw();
	}

	public Point computeSize(int wHint, int hHint, boolean changed) {
		checkWidget();
		Point e = getTotalSize(image, text);
		if (wHint == SWT.DEFAULT) {
			e.x += 2 * hIndent;
		} else {
			e.x = wHint;
		}
		if (hHint == SWT.DEFAULT) {
			e.y += 2 * vIndent;
		} else {
			e.y = hHint;
		}
		return e;
	}

	public Image getImage() {
		return image;
	}

	private Point getTotalSize(Image image, String text) {
		Point size = new Point(0, 0);

		if (image != null) {
			Rectangle r = image.getBounds();
			size.x += r.width;
			size.y += r.height;
		}

		GC gc = new GC(this);
		if (text != null && text.length() > 0) {
			Point e = gc.textExtent(text, DRAW_FLAGS);
			size.x += e.x;
			size.y = Math.max(size.y, e.y);
			if (image != null) size.x += GAP;
		} else {
			size.y = Math.max(size.y, gc.getFontMetrics().getHeight());
		}
		gc.dispose();

		return size;
	}

	public String getText() {
		return text;
	}

	void onPaint(PaintEvent event) {
		Display disp = getDisplay();
		Rectangle rect = getClientArea();
		if (rect.width == 0 || rect.height == 0) return;

		boolean shortenText = false;
		String t = text;
		Image img = image;
		int availableWidth = Math.max(0, rect.width - 2 * hIndent);
		Point extent = getTotalSize(img, t);
		if (extent.x > availableWidth) {
			shortenText = true;
		}

		GC gc = event.gc;
		String[] lines = text == null ? null : splitString(text);

		// shorten the text
		if (shortenText) {
			extent.x = 0;

			if (image != null) {
				availableWidth -= image.getBounds().width + GAP;
			}

			for (int i = 0; i < lines.length; i++) {
				Point e = gc.textExtent(lines[i], DRAW_FLAGS);
				if (e.x > availableWidth) {
					lines[i] = shortenText(gc, lines[i], availableWidth);
					extent.x = Math.max(extent.x, getTotalSize(img, lines[i]).x);
				} else {
					extent.x = Math.max(extent.x, e.x);
				}
			}

			setToolTipText(text);
		}

		// determine horizontal position
		int x = rect.x + hIndent;

		// draw the background behind the text
		Color background = getBackground();

		if (selected) {
			background = Activator.getDefault().getFormColors(disp).getColor(
					"labelex_selection_backgound");
		}

		gc.setBackground(background);
		gc.fillRectangle(rect);

		// draw the image
		if (img != null) {
			Rectangle imageRect = img.getBounds();
			gc.drawImage(img, 0, 0, imageRect.width, imageRect.height, x,
					(rect.height - imageRect.height) / 2, imageRect.width,
					imageRect.height);
			x += imageRect.width + GAP;
			extent.x -= imageRect.width + GAP;
		}

		// draw the text
		if (lines != null) {
			int lineHeight = gc.getFontMetrics().getHeight();
			int textHeight = lines.length * lineHeight;
			int lineY = Math.max(vIndent, rect.y + (rect.height - textHeight)
					/ 2);
			gc.setForeground(getForeground());
			for (int i = 0; i < lines.length; i++) {
				int lineX = x;
				gc.drawText(lines[i], lineX, lineY, DRAW_FLAGS);
				lineY += lineHeight;
			}
		}
	}

	public void setImage(Image image) {
		checkWidget();
		if (image != this.image) {
			this.image = image;
			redraw();
		}
	}

	public void setText(String text) {
		checkWidget();
		if (text == null) text = ""; //$NON-NLS-1$
		if (!text.equals(this.text)) {
			this.text = text;
			redraw();
		}
	}

	protected String shortenText(GC gc, String t, int width) {
		if (t == null) return null;
		int w = gc.textExtent(ELLIPSIS, DRAW_FLAGS).x;
		if (width <= w) return t;
		int p = t.length() - 1;
		if (p <= 0) return t;

		while (0 < p) {
			String s = t.substring(0, p);
			int l = gc.textExtent(s, DRAW_FLAGS).x;

			if (l + w > width) {
				p--;
			} else {
				break;
			}
		}

		if (p == 0) return t;
		return t.substring(0, p) + ELLIPSIS;
	}

	private String[] splitString(String text) {
		String[] lines = new String[1];
		int start = 0, pos;
		do {
			pos = text.indexOf('\n', start);
			if (pos == -1) {
				lines[lines.length - 1] = text.substring(start);
			} else {
				boolean crlf = (pos > 0) && (text.charAt(pos - 1) == '\r');
				lines[lines.length - 1] = text.substring(start, pos
						- (crlf ? 1 : 0));
				start = pos + 1;
				String[] newLines = new String[lines.length + 1];
				System.arraycopy(lines, 0, newLines, 0, lines.length);
				lines = newLines;
			}
		} while (pos != -1);
		return lines;
	}
}
