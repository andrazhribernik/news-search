package edu.stanford.snap.spinn3rHadoop.utils;

import junit.framework.Assert;

import org.junit.Test;

public class ContentCleanerTest {
	
	@Test
	public void testCssCleaner() {
		ContentCleaner cc = new ContentCleaner();
		Assert.assertEquals(cc.cssCleaner("test text"), "test text");
		
		Assert.assertEquals(cc.cssCleaner("body{backgroundcolor: lightblue;}h1 {    color: navy;    margin-left: 20px;}"), "");
		
		Assert.assertEquals(cc.cssCleaner("pre text "
				+ "input{border: 1px solid; border-color: #c0c0c0 #ededed #ededed #c0c0c0;padding:2px 0px 2px 1px;font-size:1.0em;vertical-align:middle;color:#000;background:#fff;} "
				+ "textarea{border: 1px solid; border-color: #c0c0c0 #ededed #ededed #c0c0c0;background:#fff;}"
				+ "post text"), "pre text  post text");

	}
}
