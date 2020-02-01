/*
 * @(#)VFlowLayout.java	2003-09-15
 *
 * Copyright 2002-2003 Tecnick.com S.r.l. - All rights reserved.
 */

package com.tecnick.jwtm;

import java.awt.*;

/**
 * <p>Title: VFlowLayout</p>
 * <p>Description:  A Vertical Flow Layout extends FlowLayout.
 * This class arranges components in a top-to-bottom flow.
 * This layout lets each component assume its natural (preferred) size.</p>
 *
 * <br/>Copyright (c) 2002-2006 Tecnick.com S.r.l (www.tecnick.com) Via Ugo
 * Foscolo n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * Project homepage: <a href="http://jddm.sourceforge.net" target="_blank">http://jxhtmledit.sourceforge.net</a><br/>
 * License: http://www.gnu.org/copyleft/gpl.html GPL 2
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.1.003
 */
public class VFlowLayout
extends FlowLayout {
	
	/**
	 * serialVersionUID
	 */
	private static final long serialVersionUID = 200834632704488311L;
	
	/**
	 * This value indicates that each row of components
	 * should be left-justified.
	 */
	public static final int LEFT = FlowLayout.LEFT;
	
	/**
	 * This value indicates that each row of components
	 * should be centered.
	 */
	public static final int CENTER = FlowLayout.CENTER;
	
	/**
	 * This value indicates that each row of components
	 * should be right-justified.
	 */
	public static final int RIGHT = FlowLayout.RIGHT;
	
	/**
	 * This value indicates that each column of components
	 * should be vertical aligned to the top.
	 */
	public static final int TOP = 5;
	
	/**
	 * This value indicates that each column of components
	 * should be vertical aligned to the middle.
	 */
	public static final int MIDDLE = 6;
	
	/**
	 * This value indicates that each column of components
	 * should be vertical aligned to the bottm.
	 */
	public static final int BOTTOM = 7;
	
	/**
	 * <code>align</code> is the property that determines
	 * how each row distributes empty space.
	 * It can be one of the following values:
	 * <ul>
	 * <code>LEFT</code>
	 * <code>RIGHT</code>
	 * <code>CENTER</code>
	 * </ul>
	 */
	private int halign;
	
	/**
	 * <code>valign</code> is the property that determines
	 * how each column distributes empty space.
	 * It can be one of the following values:
	 * <ul>
	 * <code>TOP</code>
	 * <code>MIDDLE</code>
	 * <code>BOTTOM</code>
	 * </ul>
	 */
	private int valign;
	
	/**
	 * The flow layout manager allows a seperation of
	 * components with gaps.  The horizontal gap will
	 * specify the space between components.
	 *
	 * @serial
	 * @see #getHgap()
	 * @see #setHgap(int)
	 */
	private int hgap;
	
	/**
	 * The flow layout manager allows a seperation of
	 * components with gaps.  The vertical gap will
	 * specify the space between rows.
	 *
	 * @serial
	 * @see #getHgap()
	 * @see #setHgap(int)
	 */
	private int vgap;
	
	/**
	 * <code>orientation</code> is the property that determines
	 * the component orientation (LTR or RTL).
	 * It can be one of the following values:
	 * <ul>
	 * <code>LEFT</code> (left-to-right)
	 * <code>RIGHT</code> (right-to-left)
	 * </ul>
	 */
	private int orientation;
	
	/**
	 * Constructs a new <code>FlowLayout</code> with a centered alignment and a
	 * default 5-unit horizontal and vertical gap.
	 */
	public VFlowLayout() {
		this(LEFT, TOP, 0, 0);
	}
	
	/**
	 * Constructs a new <code>FlowLayout</code> with the specified
	 * alignment and a default 5-unit horizontal and vertical gap.
	 * The value of the alignment argument must be one of
	 * <code>FlowLayout.LEFT</code>, <code>FlowLayout.RIGHT</code>,
	 * or <code>FlowLayout.CENTER</code>.
	 * @param halign the horizontal alignment value
	 * @param valign the vertical alignment value
	 */
	public VFlowLayout(int halign, int valign) {
		this(halign, valign, 0, 0);
	}
	
	/**
	 * Creates a new flow layout manager with the indicated alignment
	 * and the indicated horizontal and vertical gaps.
	 * <p>
	 * The value of the horizontal alignment argument must be one of
	 * <code>VFlowLayout.LEFT</code>, <code>VFlowLayout.RIGHT</code>,
	 * or <code>VFlowLayout.CENTER</code>.
	 * The value of the vertical alignment argument must be one of
	 * <code>VFlowLayout.TOP</code>,
	 * <code>VFlowLayout.MIDDLE</code>, <code>VFlowLayout.BOTTOM</code>.
	 *
	 * @param      halign   horizontal alignment value
	 * @param      valign   vertical alignment value
	 * @param      hgap    the horizontal gap between components
	 * @param      vgap    the vertical gap between components
	 */
	public VFlowLayout(int halign, int valign, int hgap, int vgap) {
		super(halign, hgap, vgap);
		this.hgap = hgap;
		this.vgap = vgap;
		this.halign = halign;
		this.valign = valign;
	}
	
	/**
	 * Gets the verical alignment for this layout.
	 * Possible values are <code>VFlowLayout.TOP</code>,
	 * <code>VFlowLayout.MIDDLE</code>, <code>VFlowLayout.BOTTOM</code>,
	 * @return     the alignment value for this layout
	 */
	public int getVAlignment() {
		return valign;
	}
	
	/**
	 * Sets the vertical alignment for this layout.
	 * Possible values are <code>VFlowLayout.TOP</code>,
	 * <code>VFlowLayout.MIDDLE</code>, <code>VFlowLayout.BOTTOM</code>.
	 * @param valign vertical alignment
	 */
	public void setVAlignment(int valign) {
		this.valign = valign;
	}
	
	/**
	 * Gets the horizontal alignment for this layout.
	 * Possible values are <code>VFlowLayout.LEFT</code>,
	 * <code>VFlowLayout.RIGHT</code>, <code>VFlowLayout.CENTER</code>,
	 * @return     the alignment value for this layout
	 */
	public int getHAlignment() {
		return halign;
	}
	
	/**
	 * Sets the horizontal alignment for this layout.
	 * Possible values are <code>VFlowLayout.TOP</code>,
	 * <code>VFlowLayout.MIDDLE</code>, <code>VFlowLayout.BOTTOM</code>,
	 * @param halign vertical alignment
	 */
	public void setHAlignment(int halign) {
		this.halign = halign;
	}
	
	/**
	 * Sets the component orientation.
	 * Possible values are <code>VFlowLayout.LEFT</code>,
	 * <code>VFlowLayout.RIGHT</code>.
	 * @param o orientation
	 */
	public void setorientation(int o) {
		this.orientation = o;
	}
	
	/**
	 * Gets the horizontal gap between components.
	 * @return     the horizontal gap between components
	 */
	public int getHgap() {
		return hgap;
	}
	
	/**
	 * Sets the horizontal gap between components.
	 * @param hgap the horizontal gap between components
	 */
	public void setHgap(int hgap) {
		this.hgap = hgap;
	}
	
	/**
	 * Gets the vertical gap between components.
	 * @return     the vertical gap between components
	 */
	public int getVgap() {
		return vgap;
	}
	
	/**
	 * Sets the vertical gap between components.
	 * @param vgap the vertical gap between components
	 */
	public void setVgap(int vgap) {
		this.vgap = vgap;
	}
	
	/**
	 * Returns the preferred dimensions for this layout given the
	 * <i>visible</i> components in the specified target container.
	 * @param target the component which needs to be laid out
	 * @return    the preferred dimensions to lay out the
	 *            subcomponents of the specified container
	 * @see Container
	 * @see #minimumLayoutSize
	 * @see       java.awt.Container#getPreferredSize
	 */
	public Dimension preferredLayoutSize(Container target) {
		synchronized (target.getTreeLock()) {
			Dimension dim = new Dimension(0, 0);
			int nmembers = target.getComponentCount();
			boolean firstVisibleComponent = true;
			
			for (int i = 0; i < nmembers; i++) { //for each component
				Component m = target.getComponent(i);
				if (m.isVisible()) {
					Dimension d = m.getPreferredSize();
					dim.width = Math.max(dim.width, d.width);
					if (firstVisibleComponent) {
						firstVisibleComponent = false;
					}
					else {
						dim.height += vgap;
					}
					dim.height += d.height;
				}
			}
			Insets insets = target.getInsets();
			dim.width += insets.left + insets.right + hgap * 2;
			dim.height += insets.top + insets.bottom + vgap * 2;
			return dim;
		}
	}
	
	/**
	 * Returns the minimum dimensions needed to layout the <i>visible</i>
	 * components contained in the specified target container.
	 * @param target the component which needs to be laid out
	 * @return    the minimum dimensions to lay out the
	 *            subcomponents of the specified container
	 * @see #preferredLayoutSize
	 * @see       java.awt.Container
	 * @see       java.awt.Container#doLayout
	 */
	public Dimension minimumLayoutSize(Container target) {
		synchronized (target.getTreeLock()) {
			Dimension dim = new Dimension(0, 0);
			int nmembers = target.getComponentCount();
			
			for (int i = 0; i < nmembers; i++) {
				Component m = target.getComponent(i);
				if (m.isVisible()) {
					Dimension d = m.getMinimumSize();
					dim.width = Math.max(dim.width, d.width);
					if (i > 0) {
						dim.height += vgap;
					}
					dim.height += d.height;
				}
			}
			Insets insets = target.getInsets();
			dim.width += insets.left + insets.right + hgap * 2;
			dim.height += insets.top + insets.bottom + vgap * 2;
			return dim;
		}
	}
	
	/**
	 * Centers the elements in the specified column, if there is any slack.
	 * @param target the component which needs to be moved
	 * @param x the x coordinate
	 * @param y the y coordinate
	 * @param width the width dimensions
	 * @param height the height dimensions
	 * @param colStart the beginning of the column
	 * @param colEnd the the ending of the column
	 * @param ltr orientation
	 */
	private void moveComponents(Container target, int x, int y, int width,
			int height,
			int colStart, int colEnd, boolean ltr) {
		synchronized (target.getTreeLock()) {
			switch (halign) {
			case LEFT: {
				x += ltr ? 0 : width;
				break;
			}
			case CENTER: {
				x += width / 2;
				break;
			}
			case RIGHT: {
				x += ltr ? width : 0;
				break;
			}
			}
			switch (valign) {
			case TOP: {
				break;
			}
			case MIDDLE: {
				y += height / 2;
				break;
			}
			case BOTTOM: {
				y += height;
				break;
			}
			}
			
			for (int i = colStart; i < colEnd; i++) {
				Component m = target.getComponent(i);
				if (m.isVisible()) {
					if (ltr) {
						m.setLocation(x, y);
					}
					else {
						m.setLocation(target.getSize().width - x - m.getSize().width, y);
					}
					y += m.getSize().height + vgap;
				}
			}
		}
	}
	
	/**
	 * Lays out the container. This method lets each component take
	 * its preferred size by reshaping the components in the
	 * target container in order to satisfy the alignment of
	 * this <code>FlowLayout</code> object.
	 * @param target the specified component being laid out
	 * @see Container
	 * @see       java.awt.Container#doLayout
	 */
	public void layoutContainer(Container target) {
		synchronized (target.getTreeLock()) {
			Insets insets = target.getInsets();
			int maxheight = target.getSize().height -
			(insets.top + insets.bottom + vgap * 2);
			int nmembers = target.getComponentCount();
			int x = insets.left + hgap, y = 0;
			int colw = 0, start = 0;
			
			boolean ltr = true;
			
			switch (orientation) {
			case RIGHT: {
				ltr = false;
				break;
			}
			default:
			case LEFT: {
				ltr = true;
				break;
			}
			
			}
			
			for (int i = 0; i < nmembers; i++) {
				Component m = target.getComponent(i);
				if (m.isVisible()) {
					Dimension d = m.getPreferredSize();
					m.setSize(d.width, d.height);
					
					if ( (y == 0) || ( (y + d.height) <= maxheight)) {
						if (y > 0) {
							y += vgap;
						}
						y += d.height;
						colw = Math.max(colw, d.width);
					}
					else {
						moveComponents(target, x, insets.top + vgap, colw, maxheight - y, start,
								i, ltr);
						y = d.height;
						x += hgap + colw;
						colw = d.width;
						start = i;
					}
				}
			}
			moveComponents(target, x, insets.top + vgap, colw, maxheight - y, start,
					nmembers, ltr);
		}
	}
	
}
