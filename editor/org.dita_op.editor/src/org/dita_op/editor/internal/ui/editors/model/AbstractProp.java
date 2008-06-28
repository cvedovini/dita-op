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
package org.dita_op.editor.internal.ui.editors.model;

import java.beans.PropertyChangeEvent;
import java.beans.PropertyChangeListener;
import java.util.ArrayList;
import java.util.List;

import org.eclipse.swt.graphics.RGB;

public abstract class AbstractProp {

	private String value;
	private String action;
	private RGB color;
	private RGB backColor;
	private String style;
	private Flag startFlag;
	private Flag endFlag;

	private final List<PropertyChangeListener> listeners = new ArrayList<PropertyChangeListener>();

	public AbstractProp() {
	}

	/**
	 * @return the attribute
	 */
	public abstract String getAttribute();

	/**
	 * @return the value
	 */
	public String getValue() {
		return value;
	}

	/**
	 * @param value
	 *            the value to set
	 */
	public void setValue(String value) {
		String oldValue = this.value;
		this.value = value;
		firePropertyChanged("value", oldValue, value); //$NON-NLS-1$
	}

	/**
	 * @return the action
	 */
	public String getAction() {
		return action;
	}

	/**
	 * @param action
	 *            the action to set
	 */
	public void setAction(String action) {
		String oldAction = this.action;
		this.action = action;
		firePropertyChanged("action", oldAction, action); //$NON-NLS-1$
	}

	/**
	 * @return the color
	 */
	public RGB getColor() {
		return color;
	}

	/**
	 * @param color
	 *            the color to set
	 */
	public void setColor(RGB color) {
		RGB oldColor = this.color;
		this.color = color;
		firePropertyChanged("color", oldColor, color); //$NON-NLS-1$
	}

	/**
	 * @return the backColor
	 */
	public RGB getBackColor() {
		return backColor;
	}

	/**
	 * @param backColor
	 *            the backColor to set
	 */
	public void setBackColor(RGB backColor) {
		RGB oldBackColor = this.backColor;
		this.backColor = backColor;
		firePropertyChanged("backColor", oldBackColor, backColor); //$NON-NLS-1$
	}

	/**
	 * @return the style
	 */
	public String getStyle() {
		return style;
	}

	/**
	 * @param style
	 *            the style to set
	 */
	public void setStyle(String style) {
		String oldStyle = this.style;
		this.style = style;
		firePropertyChanged("style", oldStyle, style); //$NON-NLS-1$
	}

	/**
	 * @return the startFlag
	 */
	public Flag getStartFlag() {
		return startFlag;
	}

	/**
	 * @param startFlag
	 *            the startFlag to set
	 */
	public void setStartFlag(Flag startFlag) {
		Flag oldStartFlag = this.startFlag;
		this.startFlag = startFlag;
		firePropertyChanged("startFlag", oldStartFlag, startFlag); //$NON-NLS-1$
	}

	/**
	 * @return the endFlag
	 */
	public Flag getEndFlag() {
		return endFlag;
	}

	/**
	 * @param endFlag
	 *            the endFlag to set
	 */
	public void setEndFlag(Flag endFlag) {
		Flag oldEndFlag = this.endFlag;
		this.endFlag = endFlag;
		firePropertyChanged("endFlag", oldEndFlag, endFlag); //$NON-NLS-1$
	}

	public void addPropertyChangeListener(PropertyChangeListener listener) {
		listeners.add(listener);
	}

	public void removePropertyChangeListener(PropertyChangeListener listener) {
		listeners.remove(listener);
	}

	protected void firePropertyChanged(String propertyName, Object oldValue,
			Object newValue) {
		PropertyChangeEvent evt = new PropertyChangeEvent(this, propertyName,
				oldValue, newValue);

		for (PropertyChangeListener listener : listeners) {
			listener.propertyChange(evt);
		}
	}
}
