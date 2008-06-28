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
package org.dita_op.editor.internal.utils;

import java.util.HashMap;
import java.util.Map;

import org.eclipse.swt.graphics.RGB;

public class RGBUtils {

	public static final RGB AQUA = new RGB(0, 255, 255);
	public static final RGB BLACK = new RGB(0, 0, 0);
	public static final RGB BLUE = new RGB(0, 0, 255);
	public static final RGB FUCHSIA = new RGB(255, 0, 255);
	public static final RGB GRAY = new RGB(128, 128, 128);
	public static final RGB GREEN = new RGB(0, 128, 0);
	public static final RGB LIME = new RGB(0, 255, 0);
	public static final RGB MAROON = new RGB(128, 0, 0);
	public static final RGB NAVY = new RGB(0, 0, 128);
	public static final RGB OLIVE = new RGB(128, 128, 0);
	public static final RGB PURPLE = new RGB(128, 0, 128);
	public static final RGB RED = new RGB(255, 0, 0);
	public static final RGB SILVER = new RGB(192, 192, 192);
	public static final RGB TEAL = new RGB(0, 128, 128);;
	public static final RGB WHITE = new RGB(255, 255, 255);
	public static final RGB YELLOW = new RGB(255, 255, 0);

	private static final Map<String, RGB> NAME2RGB = new HashMap<String, RGB>();
	private static final Map<RGB, String> RGB2NAME = new HashMap<RGB, String>();

	static {
		register("aqua", AQUA); //$NON-NLS-1$
		register("black", BLACK); //$NON-NLS-1$
		register("blue", BLUE); //$NON-NLS-1$
		register("fuchsia", FUCHSIA); //$NON-NLS-1$
		register("gray", GRAY); //$NON-NLS-1$
		register("green", GREEN); //$NON-NLS-1$
		register("lime", LIME); //$NON-NLS-1$
		register("maroon", MAROON); //$NON-NLS-1$
		register("navy", NAVY); //$NON-NLS-1$
		register("olive", OLIVE); //$NON-NLS-1$
		register("purple", PURPLE); //$NON-NLS-1$
		register("red", RED); //$NON-NLS-1$
		register("silver", SILVER); //$NON-NLS-1$
		register("teal", TEAL); //$NON-NLS-1$
		register("white", WHITE); //$NON-NLS-1$
		register("yellow", YELLOW); //$NON-NLS-1$
	}

	private static void register(String name, RGB rgb) {
		RGB2NAME.put(rgb, name);
		NAME2RGB.put(name, rgb);
	}

	public static RGB parse(String value) {
		if (value == null) {
			return null;
		}

		if (NAME2RGB.containsKey(value)) {
			return NAME2RGB.get(value);
		}

		try {
			Integer intval = Integer.decode(value);
			int i = intval.intValue();
			return new RGB((i >> 16) & 0xFF, (i >> 8) & 0xFF, i & 0xFF);
		} catch (NumberFormatException ex) {
			return null;
		}
	}

	public static String toString(RGB rgb) {
		if (rgb == null) {
			return null;
		}

		if (RGB2NAME.containsKey(rgb)) {
			return RGB2NAME.get(rgb);
		}

		StringBuilder buffer = new StringBuilder(7);

		buffer.append('#');
		buffer.append(toHex(rgb.red));
		buffer.append(toHex(rgb.green));
		buffer.append(toHex(rgb.blue));
		return buffer.toString();
	}

	private static String toHex(int i) {
		StringBuilder builder = new StringBuilder(2);

		if (i < 16) {
			builder.append('0');
		}

		builder.append(Integer.toHexString(i));
		return builder.toString().toUpperCase();
	}
}
