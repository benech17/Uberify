#include<stdio.h>
#include<stdlib.h>
#include<math.h>
int main()
{
    int a,b,max;
    printf("Entrez deux entiers:\n");
    scanf("%d%d",&a,&b);
    max = (a+b+abs(a-b))/2;
    printf("Le max de %d et %d est: %d.\n",a,b,max);
    system("pause");
    return 0;
}