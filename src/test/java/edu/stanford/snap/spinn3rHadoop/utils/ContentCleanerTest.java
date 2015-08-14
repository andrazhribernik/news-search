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
	
	@Test
	public void testCharactersCleaner() {
		ContentCleaner cc = new ContentCleaner();
		Assert.assertEquals(cc.charactersCleaner("test text"), "test text");
		Assert.assertEquals(cc.charactersCleaner("??? ??? ???????"), "");
		Assert.assertEquals(cc.charactersCleaner("what is this text about!?? knowbody knows!"), "what is this text about!?? knowbody knows!");
		Assert.assertEquals(cc.charactersCleaner("One more test????? ???? ???? ab??b ab????a"), "One more test ab??b aba");
	}
	
	@Test
	public void testHtmlCleaner() {
		ContentCleaner cc = new ContentCleaner();
		Assert.assertEquals(cc.htmlCleaner("test text"), "test text");
		
		Assert.assertEquals(cc.htmlCleaner("\" size=\"39\" onclick=\"highlight(getelementbyid('html-code')); apitrack('copy_image_html');\" /> enviar mediante correo electrónico "), 
										   "\"  enviar mediante correo electrónico ");
		Assert.assertEquals(cc.htmlCleaner("new: craziest celebrity twitter pics\" width=\"101\" height=\"56\" /> new: craziest celebrity twitter pics\">\""), 
										   "new: craziest celebrity twitter pics\"  new: craziest celebrity twitter pics\"\"");
		Assert.assertEquals(cc.htmlCleaner("her own . meta itemprop = `` datePublished '' content = `` 2014-02-05 17:50:38 '' > Mass"), 
				   						   "her own . Mass");
		
		Assert.assertEquals(cc.htmlCleaner("her own . meta itemprop = `` datePublished '' content = `` 2014-02-05 17:50:38 '' > Mass her own . meta itemprop = `` datePublished '' content = `` 2014-02-05 17:50:38 '' > Mass"), 
				   						   "her own . Mass her own . Mass");
		
	}
	
	@Test
	public void testWordpressCleaner() {
		ContentCleaner cc = new ContentCleaner();
		Assert.assertEquals(cc.wordpressCleaner("test text"), "test text");
		Assert.assertEquals(cc.wordpressCleaner("allowed to go on. [ youtube={jumble of numbers after = in url} ] i thought"), 
												"allowed to go on.  i thought");
	}
}
