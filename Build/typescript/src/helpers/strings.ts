class Strings {
  public static stripHtmlTags(text: string): string {
    text = text.replace(/(<([^>]+)>)/gi, " ")
    text = Strings.stripSpaces(text)
    return text
  }

  public static stripSpaces(text: string): string {
    // Replaces multiple spaces with a single space.
    text = text.replace(/\s{2,}/g, " ")

    // Replaces space(s) followed by a period, if the period is the last character, with only a period.
    text = text.replace(/\s\.$/, ".")

    // Removes first/last character if it's a space.
    text = text.replace(/^\s+|\s+$/g, "")

    return text
  }
}

export default Strings
