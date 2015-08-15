package edu.stanford.snap.spinn3rHadoop.classifiers;

import junit.framework.Assert;

import org.junit.Test;

import edu.stanford.snap.spinn3rHadoop.utils.ContentCleaner;

public class TextVsSpamClassifierTest {
	
	@Test
	public void testNumberOfSentence() {
		TextVsSpamClassifier classifier = new TextVsSpamClassifier();
		Assert.assertEquals(3, classifier.getNumberOfSentences("First sencetence. Second Sentence. Third sentence."));
		Assert.assertEquals(1, classifier.getNumberOfSentences("First sencetence"));
		Assert.assertEquals(1, classifier.getNumberOfSentences("First sencetence..."));
	}
	
	@Test
	public void testNumberOfWords() {
		TextVsSpamClassifier classifier = new TextVsSpamClassifier();
		Assert.assertEquals(6, classifier.getNumberOfWords("First sencetence. Second Sentence. Third sentence."));
		Assert.assertEquals(2, classifier.getNumberOfWords("First sencetence"));
	}
	
	@Test
	public void testRelativeNumberOfStopwords() {
		TextVsSpamClassifier classifier = new TextVsSpamClassifier();
		Assert.assertEquals(0.4, classifier.getRelativeNumberOfStopwords("This is a great day."));
		Assert.assertEquals(0, classifier.getRelativeNumberOfStopwords("First"));
	}
}
