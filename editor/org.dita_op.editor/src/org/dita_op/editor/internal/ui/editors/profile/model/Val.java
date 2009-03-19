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

import java.util.ArrayList;
import java.util.List;

import org.eclipse.swt.graphics.RGB;

public class Val {

	private RGB foregroundConflictColor;
	private RGB backgroundConflictColor;
	private List<AbstractProp> props = new ArrayList<AbstractProp>();

	Val() {
	}

	/**
	 * @return the foregroundConflictColor
	 */
	public RGB getForegroundConflictColor() {
		return foregroundConflictColor;
	}

	/**
	 * @param foregroundConflictColor
	 *            the foregroundConflictColor to set
	 */
	public void setForegroundConflictColor(RGB foregroundConflictColor) {
		this.foregroundConflictColor = foregroundConflictColor;
	}

	/**
	 * @return the backgroundConflictColor
	 */
	public RGB getBackgroundConflictColor() {
		return backgroundConflictColor;
	}

	/**
	 * @param backgroundConflictColor
	 *            the backgroundConflictColor to set
	 */
	public void setBackgroundConflictColor(RGB backgroundConflictColor) {
		this.backgroundConflictColor = backgroundConflictColor;
	}

	/**
	 * @return the props
	 */
	public List<AbstractProp> getProps() {
		return props;
	}

	/**
	 * @param props
	 *            the props to set
	 */
	public void setProps(List<AbstractProp> props) {
		this.props = props;
	}

}
