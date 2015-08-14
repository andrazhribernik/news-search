package edu.stanford.snap.spinn3rHadoop.classifiers;

import edu.stanford.snap.spinn3rHadoop.utils.Spinn3rDocument;

public class TextVsSpamClassifier implements Classifier {

	public int getNumberOfSentences(String content) {
		return content.split("[\\.]\\s").length;
	}
	
	public double getRelativeNumberOfStopWords(String content) {
		return 0.0;
	}
	
	public int getNumberOfWords(String content) {
		return content.split("\\s").length;
	}
	
	@Override
	public String getClass(Spinn3rDocument doc) {
		return null;
	}

}
