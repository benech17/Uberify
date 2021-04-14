import java.util.Scanner;
public class ledoubleDunNombre {
public static void main(String[] args) {
Scanner nombre=new Scanner(System.in);
		   System.out.println("Veuillez saisir un nombre : ");
		   double x=nombre.nextDouble();
		   double y=2*x;
		   System.out.println("le double de : " + x +" est " + y);	
		   nombre.close();
	}
}