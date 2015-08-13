package edu.stanford.snap.spinn3rHadoop.utils;

public class ContentCleaner {
	
	public String cssCleaner(String text) {
		return text.replaceAll("(\\w+)?(\\s*>\\s*)?(#\\w+)?\\s*(\\.\\w+)?\\s*\\{(.+?:.+?)\\}" , "");
	}

}
