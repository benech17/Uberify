#include<stdio.h>
#include<stdlib.h>
int main()
{
    float x,y,p;
    printf("Entrez deux reels:\n");
    scanf("%f%f",&x,&y);
    p=x*y;
    printf("le produit de %f et %f est: %.3f\n",x,y,p);
    system("pause");
    return 0;
}