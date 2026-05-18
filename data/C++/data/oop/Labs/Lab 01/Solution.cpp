#include <iostream>
using namespace std;
int main()
{

    //------------------------------------------TASK 1------------------------------------------
    int a = 0;
    cout << "Integer 01:" << endl;
    cin >> a;
    int b = 0;
    cout << "Integer 02:" << endl;
    cin >> b;
    int sum = a + b;
    cout << "Sum: " << sum << endl;
    cout << "Memory Address of Integer 01: " << &a << endl;
    cout << "Memory Address of Integer 02: " << &b << endl;
    cout << "Memory Address of Sum: " << &sum << endl;

    // ------------------------------------------TASK 2------------------------------------------
    int a2 = 0;
    cout << "Integer 01:" << endl;
    cin >> a2;
    int b2 = 0;
    cout << "Integer 02:" << endl;
    cin >> b2;
    = &a2 - &b2;
    return 0;
}