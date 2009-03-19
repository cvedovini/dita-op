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
package org.dita_op.editor.internal.ui.editors.profile.model;

public class Flag {

	private String imageRef;
	private String altText;

	public Flag() {
	}

	/**
	 * @return the imageRef
	 */
	public String getImageRef() {
		return imageRef;
	}

	/**
	 * @param imageRef
	 *            the imageRef to set
	 */
	public void setImageRef(String imageRef) {
		this.imageRef = imageRef;
	}

	/**
	 * @return the altText
	 */
	public String getAltText() {
		return altText;
	}

	/**
	 * @param altText
	 *            the altText to set
	 */
	public void setAltText(String altText) {
		this.altText = altText;
	}

}
