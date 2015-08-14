package edu.stanford.snap.spinn3rHadoop.utils;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class ContentCleaner {
	private static final int HTML_WINDOW_SIZE = 150;
	private static final String HTML_PATTERN = "[^\\s]+\\s{0,2}=\\s{0,2}([\"\\']).*\\1";
	
	public String cssCleaner(String text) {
		return text.replaceAll("(\\w+)?(\\s*>\\s*)?(#\\w+)?\\s*(\\.\\w+)?\\s*\\{(.+?:.+?)\\}" , "");
	}
	
	public String charactersCleaner(String text) {
		text = text.replaceAll("\\?\\s\\?" , "??");
		return text.replaceAll("\\?{3,}", "");
	}
	
	public String htmlCleaner(String text) {
		String[] textSplited = text.split(">");
		for (int i = 0; i < textSplited.length; i++) {
			int beginIndex = (textSplited[i].length() > HTML_WINDOW_SIZE) ? textSplited[i].length() - HTML_WINDOW_SIZE : 0;
			String htmlString = textSplited[i].substring(beginIndex);
			
			Pattern pattern = Pattern.compile(HTML_PATTERN);
			Matcher matcher = pattern.matcher(htmlString);
			if (matcher.find()) {
				textSplited[i] = textSplited[i].substring(0, beginIndex) + textSplited[i].substring(beginIndex, matcher.start());
			}
		}
		
		String finalText = "";
		for (String part: textSplited) {
			finalText += part;
		}
		return finalText;
	}

}
