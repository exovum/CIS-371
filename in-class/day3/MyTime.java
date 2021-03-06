import java.io.IOException;
import java.util.ArrayList;
import java.util.List;
import java.util.Scanner;

/**
 */
public class MyTime {

  /**
   * Run the {@code who} utility and return the results
   *
   * @return the set of lines generated by {@code who} as a {@code List}
   */
  public static List<String> mytime() {

    // place to store the output
    ArrayList<String> lines = new ArrayList<String>();

    try {
        // Run the mytime bash script
      Process p = Runtime.getRuntime().exec("bash mytime");

      // Grab each line generated and place it in a List
      Scanner input = new Scanner(p.getInputStream());
      while (input.hasNext()) {
        lines.add(input.nextLine());
      }
    } catch (IOException e) {
      lines.add("There was a problem: " + e);
    }
    return lines;
  }


  /**
   * Generate an HTML document containing the output of {@code who}
   *
   * @return A {@code String} containing the entire document.
   */
  public static String mytimeHTML() {
    StringBuffer buffer = new StringBuffer();
    buffer.append("<html>\n");
    buffer.append("<head>\n");
    buffer.append("<title>Who is logged in?</title>\n");
    buffer.append("<style type=\"text/css\">\n");
    buffer.append("th, td { margin-right: 10px; padding-right: 10px;}\n");
    buffer.append("th, td { text-align: left}\n");
    buffer.append("</style>\n");
    buffer.append("</head>\n");
    buffer.append("<body>\n");
    buffer.append("<h2>Current Time</h2>\n");
    for (String line : mytime()) {
        String[] splitLine = line.split(" ");
        String am = splitLine[4];
        if(am.equals("AM")) {
            buffer.append("<h3>Good Morning!</h3>\n");
        } else {
            buffer.append("<h3>Good Afternoon!</h3>\n");
        }
        String[] timeSplit = splitLine[3].split(":");
        String day = getDayOfTheWeek(Integer.parseInt(timeSplit[0]));
        buffer.append("<h4>Hey! It's " + day + "</h4>\n");

        buffer.append("<table>\n");
        buffer.append("<tr><th>HOUR</th><th>MIN</th><th>SEC</th></tr>\n");
        //Scanner parts = new Scanner(line);

        buffer.append("<tr>\n");

        buffer.append("<td>"+timeSplit[1]+"</td>\n");
        buffer.append("<td>"+timeSplit[2]+"</td>\n");
        buffer.append("<td>"+timeSplit[3]+"</td>\n");
        buffer.append("</tr>\n");
    }
    buffer.append("</table>\n");
    buffer.append("</body>\n");
    buffer.append("</html>\n");
    return buffer.toString();
  }

  public static String getDayOfTheWeek(int day) {
      switch(day) {
          case 1:
            return "Monday";
          case 2:
            return "Tuesday";
          case 3:
            return "Wednesday";
          case 4:
            return "Thursday";
          case 5:
            return "Friday";
          case 6:
            return "Saturday";
          case 7:
            return "Sunday";
      }
      return "Noday";
  }


  public static void main(String[] args) {
  }
}
