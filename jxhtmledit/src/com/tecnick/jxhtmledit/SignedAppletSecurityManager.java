package com.tecnick.jxhtmledit;

import java.security.Permission;

/**
 * Essentially - allow all permissions by overriding the checkPermissions
 * to never throw access exceptions...
 */
final class SignedAppletSecurityManager
extends SecurityManager {
	public void checkPermission(Permission perm) {}
	
	public void checkPermission(Permission perm, Object context) {}
}
