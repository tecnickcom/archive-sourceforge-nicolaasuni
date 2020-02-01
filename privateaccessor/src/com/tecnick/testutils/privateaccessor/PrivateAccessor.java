package com.tecnick.testutils.privateaccessor;

import java.lang.reflect.Field;

import junit.framework.Assert;

/**
 * Provides access to private members in classes.<br/><br/>
 * Copyright (c) 2005 Tecnick.com S.r.l (www.tecnick.com) Via Ugo Foscolo
 * n.19 - 09045 Quartu Sant'Elena (CA) - ITALY - www.tecnick.com -
 * info@tecnick.com <br/>
 * License: http://www.gnu.org/copyleft/lesser.html LGPL
 * 
 * @author Nicola Asuni [www.tecnick.com].
 * @version 1.0.004 [2005-12-11]
 */
public class PrivateAccessor {
	
	/**
	 * Returns the value of the field named "fieldName", on the specified object. 
	 * The value is automatically wrapped in an object if it has a primitive type.
	 * 
	 * @param obj object from which the represented field's value is to be extracted
	 * @param fieldName name of the field contained on obj object
	 * @return the value of the represented field in object obj; primitive values are wrapped in an appropriate object before being returned
	 */
	public static Object getPrivateField(Object obj, String fieldName) {
		// Check arguments
		Assert.assertNotNull(obj);
		Assert.assertNotNull(fieldName);
		try {
			final Field field = obj.getClass().getDeclaredField(fieldName);
			field.setAccessible(true);
			return field.get(obj);
		} catch (Exception e) {
			Assert.fail("getPrivateField Exception: " + e);
		}
		return null;
	}
	
	/**
	 * Sets the field named "fieldName" on the specified object 
	 * argument to the specified new value. 
	 * The new value is automatically unwrapped if the underlying field has a primitive type.
	 * 
	 * @param obj the object whose field should be modified
	 * @param fieldName name of the field contained on obj object
	 * @param value the new value for the field of obj  being modified
	 */
	public static void setPrivateField(Object obj, String fieldName, Object value) {
		// Check arguments
		Assert.assertNotNull(obj);
		Assert.assertNotNull(fieldName);
		try {
			final Field field = obj.getClass().getDeclaredField(fieldName);
			field.setAccessible(true);
			field.set(obj, value);
		} catch (Exception e) {
			Assert.fail("setPrivateField Exception: " + e);
		}
	}
}